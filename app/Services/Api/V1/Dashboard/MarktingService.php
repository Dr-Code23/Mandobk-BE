<?php

namespace App\Services\Api\V1\Dashboard;

use App\Models\V1\Marketing;
use App\Traits\FileOperationTrait;
use App\Traits\StringTrait;
use Illuminate\Http\JsonResponse;

class MarktingService
{
    use StringTrait;
    use FileOperationTrait;
    /**
     * Add New Ad
     *
     * @param $request
     * @return Marketing|array
     */
    public function store($request): Marketing|array
    {
        $medicine_name = $request->medicine_name;
        $company_name = $request->company_name;
        $discount = $this->setPercisionForFloatString($request->discount);

        $errors = [];
        // Check if the ad is already exists
        if (
            !Marketing::where('medicine_name', $medicine_name)
                ->where('company_name', $company_name)
                ->where('discount', $discount)
                ->value('id')
        ) {
            // Store image

            $image_name = explode('/', $request->file('img')->store('public/markting'));
            $image_name = $image_name[count($image_name) - 1];
            return Marketing::create([
                'medicine_name' => $medicine_name,
                'company_name' => $company_name,
                'discount' => $discount,
                'img' => $image_name,
            ]);
        }
        $errors['ad'][] = 'The Same Ad Is Already exists';

        return $errors;
    }


    /**
     * Update Ad
     *
     * @param $request
     * @param Marketing $ad
     * @return Marketing|array
     */
    public function update($request, $ad): Marketing|array
    {
        $medicine_name = $request->medicine_name;
        $company_name = $request->company_name;
        $discount = $this->setPercisionForFloatString($request->discount);
        // Check if the ad is already exists
        if (
            !Marketing::where('medicine_name', $medicine_name)
                ->where('company_name', $company_name)
                ->where('discount', $discount)
                ->where('id', '!=', $ad->id)
                ->first(['id'])
        ) {
            $image_name = null;
            $anyChangeOccurred = false;
            if ($request->has('img')) {
                // Delete The Old Image
                if ($this->deleteImage('markting/' . $ad->img)) {
                    // Store image
                    $image_name = explode('/', $request->file('img')->store('public/markting'));
                    $image_name = $image_name[count($image_name) - 1];
                    $anyChangeOccurred = true;
                }
            }
            if ($ad->medicine_name != $medicine_name) {
                $ad->medicine_name = $medicine_name;
                $anyChangeOccurred = true;
            }
            if ($ad->company_name != $company_name) {
                $ad->company_name = $company_name;
                $anyChangeOccurred = true;
            }
            if ($ad->discount != $discount) {
                $ad->discount = $discount;
                $anyChangeOccurred = true;
            }

            if ($image_name) {
                $ad->img = $image_name;
            }
            if ($anyChangeOccurred) {
                $ad->update();
            }
            return $ad;
        }

        $errors['ad'][] = 'The Same Ad Is Already exists';
        return $errors;
    }
}
