<?php

namespace App\Http\Controllers\Api\V1\Site\Recipes;

use App\Http\Controllers\Api\V1\Archive\ArchiveController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Site\Recipes\RecipeCollection;
use App\Models\V1\Archive;
use App\Models\V1\DoctorVisit;
use App\Models\V1\PharmacyVisit;
use App\Models\V1\Product;
use App\Models\V1\VisitorRecipe;
use App\Traits\HttpResponse;
use App\Traits\paginationTrait;
use App\Traits\roleTrait;
use App\Traits\translationTrait;
use App\Traits\userTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RecipesController extends Controller
{
    use userTrait;
    use HttpResponse;
    use translationTrait;
    use paginationTrait;
    use roleTrait;
    use translationTrait;

    public function getAllRecipes(Request $request)
    {
        $data = [];
        if ($this->getRoleNameForAuthenticatedUser() == 'visitor') {
            // then it's a visitor
            $data = VisitorRecipe::where('visitor_id', Auth::id())
                ->orderByDesc('id')
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
            $data = PharmacyVisit::whereIn('pharmacy_id', $this->getSubUsersForAuthenticatedUser())
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

    public function addRecipe(Request $request)
    {
        $errors = [];
        $products = $request->input('products');

        // Store all limited products in that array
        $limited_products = [];
        if (!is_array($products)) {
            $errors['products'] = $this->translateErrorMessage('products', 'required');
        } else {
            $index = 0;
            // Data is array
            foreach ($products as $product) {
                $validator = Validator::make($product, [
                    'product_id' => ['required', 'numeric'],
                    'quantity' => ['required', 'numeric', 'min:1'],
                ], [
                    'product_id.required' => $this->translateErrorMessage('product', 'required'),
                    'product_id.numeric' => $this->translateErrorMessage('product', 'numeric'),
                    'quantity.required' => $this->translateErrorMessage('quantity', 'required'),
                    'quantity.numeric' => $this->translateErrorMessage('quantity', 'numeric'),
                    'quantity.min' => $this->translateErrorMessage('quantity', 'min.numeric'),
                ]);

                // ? Cannot use product id cause don't sure if it's exists in array or not
                if ($validator->fails()) {
                    $errors[$index] = $validator->errors();
                }
                ++$index;
            }
            if ($errors) {
                return $this->validation_errors($errors);
            }
            // Check For Products If Exists In Admin
            // ! foreach don't add value to array !
            // foreach ($products as $product) {
            // }
            $product_count = count($products);
            for ($i = 0; $i < $product_count; ++$i) {
                // Validte only coming product
                if (isset($products[$i]['product_id'])) {
                    $product_id = $products[$i]['product_id'];
                    // Now We Can check for products with the same id

                    if (
                        $origial_product = Product::whereIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']))
                        ->where('id', $product_id)
                        ->first(['id', 'limited'])
                    ) {
                        // Check If the product is limited or not
                        if ($origial_product->limited) {
                            if (!in_array($i, $limited_products)) {
                                // Check if the product quantity is more than 1 in limited products
                                if ($products[$i]['quantity'] != 1) {
                                    $errors['limited_products_with_big_quantity'][] = $i;
                                }
                                $limited_products[] = $i;
                            } else {
                                $errors['limited'][] = $i;
                            }
                        }
                    } else { // The product not exists
                        $errors['not_exists'][] = $i;
                    }
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
                    $request->input('random_number'))
                    ->first(['id', 'details', 'alias'])
                ) {
                    // If There is any products with random number , move it to archieve
                    $move_to_archive = $request->input('move_products_to_archive_if_exists');
                    if ($move_to_archive == true) {
                        if (!ArchiveController::moveFromRandomNumberProducts(new Request(), $request->input('random_number'))) {
                            $errors['move_to_archive'] = __('validation.operation_failed');
                        }

                        if ($errors) {
                            return $this->validation_errors($errors);
                        }
                        $recipe->details = [];
                    }

                    // return $recipe->details;
                    $details = [];
                    $products_count = count($products);
                    for ($i = 0; $i < $products_count; ++$i) {
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
                        $details['products'][$i]['taken'] = false;
                    }
                    $details['doctor_name'] = $this->getAuthenticatedUserInformation()->full_name;

                    // return $details;
                    if (!$recipe->details) {
                        $recipe->details = $details;

                        $recipe->update();

                        // Add This visit To the doctor
                        DoctorVisit::create([
                            'doctor_id' => Auth::id(),
                            'visitor_recipe_id' => $recipe->id,
                        ]);

                        return $this->success(null, 'Recipe Sent Successfully');
                    } else {
                        $errors['products'] = $this->translateErrorMessage('products', 'not_empty');
                    }
                } else {
                    $errors['random_number'][] = $this->translateErrorMessage('random_number', 'not_exists');
                }
            } else {
                $errors['random_number'][] = $this->translateErrorMessage('random_number', 'invalid');
            }
        }

        return $this->validation_errors($errors);
    }

    /**
     * Summary of getProductsWithRandomNumber.
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function getProductsWithRandomNumber(Request $request)
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
        $random_number = $this->convertToInteger($request->input('random_number'));
        // valid data
        $visitor_products = VisitorRecipe::where('random_number', $random_number)->first(['details']);
        if ($visitor_products && !$visitor_products->details) {
            // Everything is valid

            return $this->resourceResponse($visitor_products->details);
        } else {
            // ? You must move old products to archive before make receipt
            return $this->validation_errors(
                [
                    'products' => $this->translateErrorMessage('products', 'not_empty'),
                ]
            );
        }
    }

    private function moveProductsToArchive(int $random_number): bool
    {
        $errors = [];
        if (is_numeric($random_number)) {
            $visitor_recipe = VisitorRecipe::where('random_number', $random_number)->first(['id', 'details']);
            if ($visitor_recipe) {
                $visitor_details = $visitor_recipe->details;
                $visitor_recipe->details = [];
                $visitor_recipe->update();
                $archive = Archive::where('random_number', $random_number)->first(['id', 'details']);
                $new_details = $visitor_details;
                if ($archive) {
                    $new_details = array_merge($archive->details, $new_details);
                    $archive->details = $new_details;
                    $archive->update();

                    return true;
                } else {
                    Archive::create([
                        'random_number' => $random_number,
                        'details' => $new_details,
                    ]);

                    return true;
                }
            }
        }

        return false;
    }
}

/*

    Visitor_recipents

    id doctor_id random_number_id details created_at updated_at

*/