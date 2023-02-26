<?php

namespace App\Services\Api\V1\Site\Recipe;

use App\Http\Resources\Api\V1\Site\Recipes\PharmacyRecipeCollection;
use App\Http\Resources\Api\V1\Site\Recipes\PharmacyRecipeResource;
use App\Models\V1\PharmacyVisit;
use App\Models\V1\VisitorRecipe;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;

class RecipeService
{
    use HttpResponse;
    public function getAllPharmacyRecipes()
    {

        return PharmacyVisit::join('visitor_recipes', 'visitor_recipes.id', 'pharmacy_visits.visitor_recipe_id')
            ->join('users as visitor_table', 'visitor_table.id', 'visitor_recipes.visitor_id')
            ->join('users as doctor_table', 'doctor_table.id', 'pharmacy_visits.doctor_id')
            ->get([
                'visitor_table.full_name as visitor_name',
                'doctor_table.full_name as doctor_name',
                'pharmacy_visits.created_at as created_at',
            ]);
    }

    public function getProductsAssociatedWithRandomNumberForPharmacy(Request $request)
    {
        $randomNumber = $request->random_number;

        if ($visitorRecipe = VisitorRecipe::where('random_number', $randomNumber)->first()) {
            return $visitorRecipe;
        }
        return false;
    }
}
