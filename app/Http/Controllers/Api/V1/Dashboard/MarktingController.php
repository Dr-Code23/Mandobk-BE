<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\marktingRequest;
use App\Http\Resources\Api\V1\Dashboard\Markting\marktingCollection;
use App\Http\Resources\Api\V1\Dashboard\Markting\marktingResource;
use App\Models\Api\V1\Markting;
use App\Traits\fileOperationTrait;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;

class MarktingController extends Controller
{
    use HttpResponse;
    use StringTrait;
    use fileOperationTrait;

    /**
     * List All Ads For Markting Role.
     *
     * @return array
     */
    public function index()
    {
        return $this->resourceResponse(new marktingCollection(Markting::all()));
    }

    /**
     * Fetch One Ad For Markting Role.
     *
     * @return array
     */
    public function show(Markting $ad)
    {
        return $this->resourceResponse(new marktingResource($ad));
    }

    public function store(marktingRequest $request)
    {
        $medicine_name = $this->sanitizeString($request->medicine_name);
        $company_name = $this->sanitizeString($request->company_name);
        $discount = $this->setPercisionForFloatString($request->discount);
        // Check if the ad is already exists
        if (!Markting::where('medicine_name', $medicine_name)->where('company_name', $company_name)->where('discount', $discount)->first(['id'])) {
            // Store image
            $image_name = explode('/', $request->file('img')->store('public/markting'));
            $image_name = $image_name[count($image_name) - 1];
            $ad = Markting::create([
                'medicine_name' => $medicine_name,
                'company_name' => $company_name,
                'discount' => $discount,
                'img' => $image_name,
            ]);

            return $this->success(new marktingResource($ad), 'Ad Created Successfully');
        }

        return $this->validation_errors(['ad' => 'The Same Ad Is Already exists']);
    }

    public function update(marktingRequest $request, Markting $ad)
    {
        $medicine_name = $this->sanitizeString($request->medicine_name);
        $company_name = $this->sanitizeString($request->company_name);
        $discount = $this->setPercisionForFloatString($request->discount);
        // Check if the ad is already exists
        if (!Markting::where('medicine_name', $medicine_name)->where('company_name', $company_name)->where('discount', $discount)->where('id', '!=', $ad->id)->first(['id'])) {
            $image_name = null;
            $anyChangeOccured = false;
            if ($request->has('img')) {
                // Delete The Old Image
                if ($this->deleteImage('markting/'.$ad->img)) {
                    // Store image
                    $image_name = explode('/', $request->file('img')->store('public/markting'));
                    $image_name = $image_name[count($image_name) - 1];
                    $anyChangeOccured = true;
                }
            }
            if ($ad->medicine_name != $medicine_name) {
                $ad->medicine_name = $medicine_name;
                $anyChangeOccured = true;
            }
            if ($ad->company_name != $company_name) {
                $ad->company_name = $company_name;
                $anyChangeOccured = true;
            }
            if ($ad->discount != $discount) {
                $ad->discount = $discount;
                $anyChangeOccured = true;
            }

            if ($image_name) {
                $ad->img = $image_name;
            }
            if ($anyChangeOccured) {
                $ad->update();

                return $this->success(new marktingResource($ad), 'Ad Updated Successfully');
            }

            return $this->noContentResponse();
        }

        return $this->validation_errors(['ad' => 'The Same Ad Is Already exists']);
    }

    public function destroy(Markting $ad)
    {
        $this->deleteImage('markting/'.$ad->img);
        $ad->delete();

        return $this->success(msg: 'Ad Deleted Successfully');
    }
}
