<?php

namespace App\Services\Api\V1\Archive;

use App\Models\V1\Archive;
use App\Models\V1\VisitorRecipe;

class ArchiveService
{
    public function moveProductsToArchive($request): bool|array
    {
        $random_number = $request->input('random_number');
        $errors = [];

        $visitor_recipe = VisitorRecipe::where('visitor_id', auth()->id())
            ->where('random_number', $random_number)
            ->first(['id', 'details']);

        // return $visitor_recipe;
        if ($visitor_recipe) {
            // updateVisitorDetails

            // $visitor_details = $visitor_recipe->details;

//            $archiveDetails = [];
            $archive = Archive::where('random_number', $random_number)->first(['id', 'details']);

//            $visitorDetails = array_merge([], $visitor_recipe->details);
            $visitorDetails = $visitor_recipe->details;
            $archiveDetails = $visitorDetails;
//            if ($visitorDetails) $archiveDetails[] = $visitorDetails;
//            if ($archive) {
//                $archiveDetails = array_merge($archiveDetails, $archive->details);
//            }
            if ($archive) {
                if ($visitorDetails) {
                    $archive->update([
                        'details' => $visitorDetails,
                    ]);
                }
            } else {
                Archive::create([
                    'random_number' => $random_number,
                    'details' => $archiveDetails,
                ]);
            }
            if ($visitorDetails) {
                $visitor_recipe->update(['details' => []]);
            }

            return true;
        }
        $errors['random_number'][] = 'Random Number Not Exists';

        return $errors;
    }
}
