<?php

namespace App\Http\Controllers\Api\V1\Site\Recipes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Site\Recipes\PharmacyRecipeRequest;
use App\Http\Requests\Api\V1\Site\Recipes\RecipeRequest;
use App\Http\Resources\Api\V1\Site\Recipe\RecipeCollection;
use App\Http\Resources\Api\V1\Site\Recipe\RecipeResource;
use App\Http\Resources\Api\V1\Site\Recipes\PharmacyRecipeCollection;
use App\Http\Resources\Api\V1\Site\VisitorRecipe\VisitorRecipeResource;
use App\Models\User;
use App\Models\V1\DoctorVisit;
use App\Models\V1\PharmacyVisit;
use App\Models\V1\Product;
use App\Models\V1\ProductInfo;
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
                ->where('details' , '!=' , '[]')
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
                    $details['products'][$i]['quantity'] = $products[$i]['quantity'].'';
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
                'random_number' => ['Random Number Has Old Products Associated With it , move it to archieve to continue']
            ]);
        }

        return $this->notFoundResponse('Random Number Not Exists');
    }

    public function getAllPharmacyRecipes(RecipeService $recipeService): JsonResponse
    {
        return $this->resourceResponse(new PharmacyRecipeCollection($recipeService->getAllPharmacyRecipes()));
    }

    public function getProductsAssociatedWithRandomNumberForPharmacy(Request $request, RecipeService $recipeService): JsonResponse
    {
        $recipe = $recipeService->getProductsAssociatedWithRandomNumberForPharmacy($request);
        if (is_bool($recipe) && !$recipe)
            return $this->notFoundResponse('Random Number Not Found');

        return $this->resourceResponse(new VisitorRecipeResource($recipe));
    }

    /**
     * @param PharmacyRecipeRequest $request
     * @param VisitorRecipe $recipe
     * @return JsonResponse
     */
    public function acceptVisitorRecipeFromPharmacy(PharmacyRecipeRequest $request, VisitorRecipe $recipe): JsonResponse
    {
        $recipeDetails = $recipe->details['products'] ?? [];
        $recipeDetailsCount = count($recipeDetails);

        $printVisitorRecipe = [];
        $errors = [];
        $validProducts = [];
        $requestData = $request->data;

        // Merge Repeated Products
        $data = [];
        foreach ($requestData as $product) {
            $productFound = false;

            for ($i = 0; $i < count($data); $i++) {
                if ($product['commercial_name'] == $data[$i]['commercial_name']) {
                    $data[$i]['quantity'] += $product['quantity'];
                    $productFound = true;
                }
            }
            if (!$productFound) $data[] = $product;
        }

        // return $data;
        for ($j = 0; $j < count($data); $j++) {
            $productFound = false;
            for ($i = 0; $i < $recipeDetailsCount; $i++) {
                if ($data[$j]['commercial_name'] == $recipeDetails[$i]['commercial_name']) {
                    // then Product In Visitor Cart

                    $productFound = true;

                    $userProduct = Product::whereIn('com_name', [
                        $data[$j]['commercial_name'],
                        $data[$j]['alternative_commercial_name'] ?? null
                    ])
                        ->whereIn('user_id', $this->getSubUsersForUser())
                        ->withSum(
                            [
                                'product_details' => fn($query) => $query->where('expire_date', '>', date('Y-m-d')),
                            ],
                            'qty'
                        )
                        ->first();

                    if ($userProduct) {
                        if ($userProduct->product_details_sum_qty >= $data[$j]['quantity']) {
                            if ($data[$j]['quantity'] == $recipeDetails[$i]['quantity']) {
                                // Then Everything Is Valid , start appending the products
                                $validProducts[] = $userProduct;
                            } else $errors[$j]['quantity'][] = 'Quantity Not Equal To Doctor Recipe Quantity';
                        } else $errors[$j]['quantity'][] = 'Quantity Is bigger Than Existing Qty Which is ' . $userProduct->product_details_sum_qty;
                    } else $errors[$j]['product'][] = 'Product Not Found';
                    // break;
                }
            }

            if (!$productFound) $errors[$j]['product'][] = 'Product Not Found in Visitor cart';
        }
        //! Very Bad Performance !
        //O (N * M * K)

        if (!$errors) {
            $saleDetails = [];
            $saleTotal = 0;
            for ($i = 0; $i < $recipeDetailsCount; $i++) {
                for ($j = 0; $j < count($validProducts); $j++) {
                    // Remove The Element From the Visitor Cart And Update User Product Quantity

                    $productDetails = ProductInfo::where('product_id', $validProducts[$j]->id)
                        ->latest('qty')
                        ->where('qty', '>', 0)
                        ->where('expire_date', '>', date('Y-m-d'))
                        ->get(['id', 'qty']);
                    if ($productDetails) {
                        $tmpVisitorProductQuantity = $recipeDetails[$i]['quantity'];
                        for ($k = 0; $k < count($productDetails) && $tmpVisitorProductQuantity > 0; $k++) {

                            // If Product Has Enough Quantity , decrease Visitor Quantity
                            if ($productDetails[$k]->qty >= $tmpVisitorProductQuantity) {
                                $saleTotal += ($tmpVisitorProductQuantity * $validProducts[$j]->sel_price);
                                $productDetails[$k]->qty -= $tmpVisitorProductQuantity;
                                $tmpVisitorProductQuantity = 0;
                            } else {
                                $saleTotal += ($productDetails[$k]->qty * $validProducts[$j]->sel_price);
                                $tmpVisitorProductQuantity -= $productDetails[$k]->qty;
                                $productDetails[$k]->qty = 0;
                            }
                            // return $productDetails[$k];
                            $productDetails[$k]->update();
                        }
                    } else $errors[$j]['quantity'][] = 'Product Has No Details With Expire Date Bigger Than Today';
                }
                if (!$errors) {
                    $recipeDetails[$i]['taken'] = true;
                    $printVisitorRecipe[] = $recipeDetails[$i];
                    $saleDetails[] = [
                        'commercial_name' => $userProduct->com_name,
                        'scientific_name' => $userProduct->sc_name,
                        'selling_price' => $userProduct->sel_price,
                        'purchase_price' => $userProduct->pur_price,
                        'quantity' => $recipeDetails[$i]['quantity']
                    ];
                    // Remove The item From Visitor Cart
                    unset($recipeDetails[$i]);
                    $recipeDetailsCount--;
                }
            }

            // $recipe->updateSubUser([
            //     'details' => $recipeDetailsCount == 0 ? [] : $recipeDetails
            // ]);


            $fromId = auth()->id();
            if ($this->getRoleNameForAuthenticatedUser() != 'pharmacy') {
                $parentId = SubUser::where('sub_user_id', auth()->id())->value('parent_id');
                if ($parentId) $fromId = $parentId;
            }
            Sale::create([
                'from_id' => $fromId,
                'to_id' => User::where('username', 'customer')->value('id'),
                'details' => $saleDetails,
                'total' => $saleTotal,
                'type' => '3'
            ]);
            return $this->success($printVisitorRecipe);
        }
        return $this->validation_errors($errors);
    }
}
