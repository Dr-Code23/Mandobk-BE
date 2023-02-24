<?php

namespace App\Services\Api\V1\Site\Recipe;

use App\Models\V1\PharmacyVisit;

class RecipeService
{
    public function getAllPharmacyRecipes()
    {
        return PharmacyVisit::with([
            'doctor_user',
            'visitor_recipe' => function ($query) {
                $query->with('visitor');
            }
        ])->get();
    }
}
