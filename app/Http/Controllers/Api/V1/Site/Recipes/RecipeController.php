<?php

namespace App\Http\Controllers\Api\V1\Site\Recipes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Site\Recipes\PharmacyRecipeRequest;
use App\Http\Requests\Api\V1\Site\Recipes\RecipeRequest;
use App\Http\Resources\Api\V1\Site\Recipe\RecipeCollection;
use App\Http\Resources\Api\V1\Site\Recipe\RecipeResource;
use App\Http\Resources\Api\V1\Site\Recipes\PharmacyRecipeCollection;
use App\Http\Resources\Api\V1\Site\Sales\SaleResource;
use App\Http\Resources\Api\V1\Site\VisitorRecipe\VisitorRecipeResource;
use App\Models\User;
use App\Models\V1\Archive;
use App\Models\V1\DoctorVisit;
use App\Models\V1\PharmacyVisit;
use App\Models\V1\Product;
use App\Models\V1\Sale;
use App\Models\V1\SubUser;
use App\Models\V1\VisitorRecipe;
use App\Services\Api\V1\Site\Recipe\RecipeService;
use App\Traits\HttpResponse;
use App\Traits\PaginationTrait;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    use UserTrait, HttpResponse, Translatable,
        PaginationTrait, RoleTrait, Translatable;

    /**
     * @return JsonResponse
     */
    public function getAllRecipes(): JsonResponse
    {
        $data = [];
        if ($this->getRoleNameForAuthenticatedUser() == 'visitor') {
            // then it's a visitor
            $data = VisitorRecipe::where('visitor_id', Auth::id())
                ->orderByDesc('id')
                ->where('details', '!=', '[]')
                ->get([
                    'random_number',
                    'alias',
                    'details',
                    'created_at',
                    'updated_at',
                ]);
        } elseif ($this->getRoleNameForAuthenticatedUser() == 'doctor') {
            $data = DoctorVisit::where('doctor_id', Auth::id())
                ->join('visitor_recipes', 'visitor_recipes.id', 'doctor_visits.visitor_recipe_id')
                ->join('users', 'users.id', 'visitor_recipes.visitor_id')
                ->orderByDesc('doctor_visits.id')
                ->select([
                    'visitor_recipes.alias as alias',
                    'doctor_visits.created_at as created_at',
                ])->get();
        } elseif ($this->roleNameIn(['pharmacy', 'pharmacy_sub_user'])) {
            $data = PharmacyVisit::whereIn('pharmacy_id', $this->getSubUsersForUser())
                ->join('users', 'users.id', 'pharmacy_visits.pharmacy_id')
                ->join('visitor_recipes', 'visitor_recipes.id', 'pharmacy_visits.visitor_recipe_id')
                ->orderByDesc('pharmacy_visits.id')
                ->get([
                    'users.full_name as doctor_name',
                    'visitor_recipes.alias as alias',
                    'pharmacy_table.created_at as created_at',
                ]);
        }
        // return $data;
        return $this->resourceResponse(new RecipeCollection($data));
    }

    /**
     * @param RecipeRequest $request
     * @return JsonResponse
     */
    public function addRecipe(RecipeRequest $request): JsonResponse
    {

        $errors = [];

        // Merge Repeated Products
        $uniqueProducts = [];
        foreach ($request->input('products') as $product) {
            $productFound = false;
            for ($i = 0; $i < count($uniqueProducts); $i++) {
                if ($product['product_id'] == $uniqueProducts[$i]['product_id']) {
                    $uniqueProducts[$i]['quantity'] += $product['quantity'];
                    $productFound = true;
                    break;
                }
            }
            if (!$productFound) $uniqueProducts[] = $product;
        }

        $products = $uniqueProducts;
        // Store all limited products in that array
        $limitedProducts = [];

        // Check For Products If Exists In Admin
        // ! foreach don't add value to array !
        // foreach ($products as $product) {
        // }
        $productsCount = count($products);
        for ($i = 0; $i < $productsCount; ++$i) {
            // Validate only coming product

            $productId = $products[$i]['product_id'];
            // Now We Can check for products with the same id

            if (
                $originalProduct = Product::whereIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']))
                    ->where('id', $productId)
                    ->first(['id', 'limited'])
            ) {
                // Check If the product is limited or not
                if ($originalProduct->limited) {
                    if (!in_array($i, $limitedProducts)) {
                        // Check if the product quantity is more than 1 in limited products
                        if ($products[$i]['quantity'] != 1) {
                            $errors['products'][$i]['limited_products_with_big_quantity'][] = $this->translateErrorMessage('product', 'limited_products_with_big_quantity');
                        }
                        $limitedProducts[] = $i;
                    } else {
                        $errors['products'][$i]['limited'][] = $this->translateErrorMessage('product', 'limited');
                    }
                }
            } else { // The product not exists
                $errors['products'][$i]['not_exists'][] = $this->translateErrorMessage('product', 'not_exists');
            }
        }
        if ($errors) {
            return $this->validation_errors($errors);
        }

        // Everything is valid so , Check Random Number Have old products or not

        // check if the request has random number
        if (is_numeric($request->input('random_number'))) {
            if ($recipe = VisitorRecipe::where(
                'random_number',
                $request->input('random_number')
            )
                ->first(['id', 'details', 'alias'])
            ) {
                $details = [];
                $productsCount = count($products);
                for ($i = 0; $i < $productsCount; ++$i) {
                    $product = $products[$i];

                    $product_info = Product::where('id', $product['product_id'])->first(
                        [
                            'sc_name',
                            'com_name',
                            'con',
                        ]
                    );
                    $details['products'][$i]['scientific_name'] = $product_info->sc_name;
                    $details['products'][$i]['commercial_name'] = $product_info->com_name;
                    $details['products'][$i]['concentrate'] = $product_info->con;
                    $details['products'][$i]['quantity'] = $products[$i]['quantity'] . '';
                    $details['products'][$i]['taken'] = false;
                }
                $details['doctor_name'] = $this->getAuthenticatedUserInformation()->full_name;

                // return $details;
                if (!$recipe->details) {
                    $recipe->details = $details;

                    $recipe->update();
                    // Add This visit To the doctor
                    $doctorRecipe = DoctorVisit::create([
                        'doctor_id' => Auth::id(),
                        'visitor_recipe_id' => $recipe->id,
                    ]);

                    $doctorRecipe->alias = $recipe->alias;
                    $doctorRecipe->created_at = date('Y-m-d H:i');
                    return $this->success(new RecipeResource($doctorRecipe), 'Recipe Sent Successfully');
                } else {
                    $errors['products'] = $this->translateErrorMessage('products', 'not_empty');
                }
            } else {
                $errors['random_number'][] = $this->translateErrorMessage('random_number', 'not_exists');
            }
        } else {
            $errors['random_number'][] = $this->translateErrorMessage('random_number', 'invalid');
        }

        return $this->validation_errors($errors);
    }

    /**
     * Summary of getProductsWithRandomNumber.
     *
     * @param Request $request
     * @return JsonResponse|array
     */
    public function getProductsWithRandomNumber(Request $request): JsonResponse|array
    {
        $validator = Validator::make($request->all(), [
            'random_number' => ['required', 'numeric'],
        ], [
            'random_number.required' => $this->translateErrorMessage('random_number', 'required'),
            'random_number.numeric' => $this->translateErrorMessage('random_number', 'numeric'),
        ]);
        if ($validator->fails()) {
            return $this->validation_errors($validator->errors());
        }
        $random_number = $request->input('random_number');
        // valid data
        $visitor_products = VisitorRecipe::where('random_number', $random_number)->first(['details']);

        if ($visitor_products) {
            if (!$visitor_products->details)
                // Everything is valid
                return $this->resourceResponse($visitor_products->details);
            return $this->validation_errors([
                'random_number' => ['Random Number Has Old Products Associated With it , move it to archive to continue']
            ]);
        }

        return $this->notFoundResponse('Random Number Not Exists');
    }

    public function getAllPharmacyRecipes(RecipeService $recipeService): JsonResponse
    {
        return $this->resourceResponse(new PharmacyRecipeCollection($recipeService->getAllPharmacyRecipes()));
    }

    public function getProductsAssociatedWithRandomNumberForPharmacy(RecipeService $recipeService): JsonResponse
    {
        $recipe = $recipeService->getProductsAssociatedWithRandomNumberForPharmacy();
        if (is_bool($recipe) && !$recipe) {
            return $this->notFoundResponse('Random Number Not Found');
        }

        return $this->resourceResponse(new VisitorRecipeResource($recipe));
    }

    /**
     * @param PharmacyRecipeRequest $request
     * @param VisitorRecipe $recipe
     * @return JsonResponse|Response
     */
    public function acceptVisitorRecipeFromPharmacy(PharmacyRecipeRequest $request, VisitorRecipe $recipe): JsonResponse|Response
    {
        if (isset($recipe->details['products']) && isset($recipe->details['doctor_name'])) {

            $doctorName = $recipe->details['doctor_name'];
            $recipeId = $recipe->id;
            $randomNumber = $recipe->random_number;
            $productsWantToTake = $request->input('data');
            $errors = [];

            // TODO Remove The Duplicates From Incoming Data
            $uniqueRequestProducts = [];
            foreach ($productsWantToTake as $product) {
                $productExists = false;
                for ($i = 0; $i < count($uniqueRequestProducts); $i++) {
                    if ($uniqueRequestProducts[$i] == $product['commercial_name']) {
                        $productExists = true;
                        break;
                    }
                }
                if (!$productExists) $uniqueRequestProducts[] = $product['commercial_name'];

            }
            // Getting Visitor Recipe Products
            $visitorProducts = $recipe->details['products'] ?? [];

            //TODO Checking If The Visitor Has The Product In His Cart
            $visitorCommercialNames = [];
            foreach ($visitorProducts as $product) {
                $visitorCommercialNames[] = $product['commercial_name'];
            }

            $cnt = 0;
            foreach ($uniqueRequestProducts as $uniqueProduct) {
                if (!in_array($uniqueProduct, $visitorCommercialNames)) {
                    $errors[$cnt]['products'] = 'Product Not Exists In Visitor Recipe';
                }
                $cnt++;
            }


            if (!$errors) {

                //TODO Get The Products For Pharmacy

                //Fetch All The Products To Validate
                $pharmacyProducts = Product::whereIn('com_name', $uniqueRequestProducts)
                    ->with(['product_details' => function ($query) {
                        $query->select(['id', 'product_id', 'qty'])
                            ->latest('qty')
                            ->where('expire_date', '>', date('Y-m-d'))
                            ->where('qty', '>', 0);
                    }])
                    ->whereIn('user_id', $this->getSubUsersForUser())
                    ->select(['id', 'com_name', 'sel_price', 'pur_price', 'sc_name'])
                    ->withSum(
                        [
                            'product_details' => function ($query) {

                                $query->where('expire_date', '>', date('Y-m-d'));
                            }
                        ], 'qty'
                    )
                    //! Putting Selected Items In get() not working !
                    ->get();

                //TODO Check If All Products Exists For Pharmacy

                $existingPharmacyCommercialNames = [];
                foreach ($pharmacyProducts as $pharmacyProduct) {
                    $existingPharmacyCommercialNames[] = $pharmacyProduct->com_name;
                }

                //TODO Loop Coming Products To Check If All Of It Have Been Fetched Or not
                $cnt = 0;
                foreach ($uniqueRequestProducts as $uniqueRequestProduct) {
                    if (!in_array($uniqueRequestProduct, $existingPharmacyCommercialNames)) {
                        $errors[$cnt]['product'] = 'Pharmacy Product not exists';
                    }
                    $cnt++;
                }

                if (!$errors) {
                    // So All Products Exists With Enough Quantity

                    //TODO Continue Accepting The Recipe

                    $takenDetails = [];
                    $totalSales = 0;
                    for ($i = 0; $i < count($visitorProducts); $i++) {

                        $visitorCommercialName = $visitorProducts[$i]['commercial_name'];
                        if (in_array($visitorCommercialName, $existingPharmacyCommercialNames)) {


                            //TODO Validate If The Product Has Enough Quantity

                            for ($j = 0; $j < count($pharmacyProducts); $j++) {
                                if ($pharmacyProducts[$j]->com_name == $visitorProducts[$i]['commercial_name']) {
                                    if (
                                        $pharmacyProducts[$j]->product_details_sum_qty
                                        < $visitorProducts[$i]['quantity']
                                    ) {
                                        $errors[$j]['product'] =
                                            'Product Has Quantity Less Visitor Quantity Which Is ' . $visitorProducts[$i]['quantity'];
                                    }
                                }
                            }
                            if (!$errors) {
                                // Check If We Can Accept All The Quantity
                                //TODO Get The Current Product To Deal With
                                $pharmacyProduct = null;

                                for ($j = 0; $j < count($pharmacyProducts); $j++) {
                                    if ($pharmacyProducts[$j]->com_name == $visitorCommercialName) {
                                        $pharmacyProduct = $pharmacyProducts[$j];
                                        break;
                                    }
                                }

                                // Decrease Details Only If It Has Enough Quantity


                                //TODO Loop Over Product Details To Decrease The Quantity
                                $tmpVisitorQuantity = $visitorProducts[$i]['quantity'];
                                $pharmacyProductDetailsCount = count($pharmacyProduct->product_details);

                                for ($j = 0; $j < $pharmacyProductDetailsCount && $tmpVisitorQuantity; $j++) {

                                    if ($pharmacyProduct->product_details[$j]->qty >= $tmpVisitorQuantity) {
                                        $pharmacyProduct->product_details[$j]->qty -= $tmpVisitorQuantity;
                                        $tmpVisitorQuantity = 0;
                                    } else {
                                        $tmpVisitorQuantity -= $pharmacyProduct->product_details[$j]->qty;
                                        $pharmacyProduct->product_details[$j]->qty = 0;
                                    }
                                    $pharmacyProduct->product_details[$j]->save();
                                }
                                $takenDetails[] = $pharmacyProduct->com_name;
                                $totalSales += ($visitorProducts[$i]['quantity'] * $pharmacyProduct->sel_price);
                            }
                        }
                    }

                    if (!$errors) {
                        //TODO Start Removing The Products From Visitor Cart
                        $saleProducts = [];
                        $archiveDetails = ['products' => []];
                        $existingProducts = ['products' => []];
                        $takenProductsQuantities = [];
                        foreach ($visitorProducts as $visitorProduct) {
                            if (!in_array($visitorProduct['commercial_name'], $takenDetails)) {
                                $existingProducts['products'][] = $visitorProduct;
                            } else {
                                $visitorProduct['taken'] = true;
                                $archiveDetails['products'][] = $visitorProduct;
                                $takenProductsQuantities[$visitorProduct['commercial_name']] = $visitorProduct['quantity'];
                            }
                        }

                        foreach ($pharmacyProducts as $pharmacyProduct) {
                            if (in_array($pharmacyProduct->com_name, $takenDetails)) {
                                $saleProducts[] = [
                                    'commercial_name' => $pharmacyProduct->com_name,
                                    'scientific_name' => $pharmacyProduct->sc_name,
                                    'quantity' => $takenProductsQuantities[$pharmacyProduct->com_name],
                                    'selling_price' => $pharmacyProduct->sel_price,
                                    'purchase_price' => $pharmacyProduct->pur_price,
                                ];
                            }
                        }
                        //TODO Add Sales To Pharmacy

                        $fromId = auth()->id();
                        if ($this->getRoleNameForAuthenticatedUser() != 'pharmacy') {
                            $parentId = SubUser::where('sub_user_id', $fromId)->value('parent_id');
                            if ($parentId) $fromId = $parentId;
                        }

                        //TODO Update The Recipe
                        $recipe->details = $existingProducts['products'] ?: [];
                        $recipe->save();

                        //TODO Add To Sales
                        $saleDetails = Sale::create([
                            'from_id' => $fromId,
                            'to_id' => User::where('username', 'customer')->value('id'),
                            'details' => $saleProducts,
                            'total' => $totalSales,
                            'type' => '3'
                        ]);

                        //TODO Add Purchased Products To Archive
                        $archiveDetails['doctor_name'] = $doctorName;

                        Archive::updateOrCreate(['random_number' =>$randomNumber], [
                            'random_number' => $recipe->random_number,
                            'details' => $archiveDetails
                        ]);

                        //TODO Create Pharmacy Visit For That Visit
                        PharmacyVisit::create([
                            'visitor_recipe_id' => $recipeId,
                            'doctor_id' => DoctorVisit::where('visitor_recipe_id', $recipeId)->value('doctor_id'),
                            'pharmacy_id' => $fromId,
                        ]);
                        $saleDetails->full_name = 'Customer';

                        return $this->createdResponse(new SaleResource($saleDetails));
                    }
                }
            }

            return $this->validation_errors($errors);
        }

        return $this->noContentResponse();
    }
}


/*
 - What We Want to Do :

    -- 1 Catch The Products From The Request And Validate it (Done)

    -- 2 Remove The Duplicates From Incoming Request (Done)

    -- 3 Get The Visitor Recipe Products

    -- 4 Checking If The Visitor Has The Product

    --5 Check If Pharmacy Own The Product

    --6 We Want To Check If The Pharmacy Has All Products Sent With Enough Quantity
*/
