<?php

namespace App\Http\Controllers\Api\Web\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Dashboard\dataEntryRequest;
use App\Http\Resources\Api\Web\V1\Dashboard\DataEntry\dataEntryCollection;
use App\Http\Resources\Api\Web\V1\Dashboard\DataEntry\dataEntryResource;
use App\Models\Api\Web\V1\DataEntry;
use App\Traits\dateTrait;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\translationTrait;
use Illuminate\Http\Request;

class dataEntryController extends Controller
{
    use translationTrait;
    use StringTrait;
    use HttpResponse;
    use translationTrait;
    use dateTrait;

    private string $translationFileName = 'Dashboard/dataEntryTranslationFile.';

    public function index()
    {
        return $this->resourceResponse(new dataEntryCollection(DataEntry::all()));
    }

    /**
     * Get Translated Content.
     *
     * @return \App\Http\Resources\Api\Web\V1\Translation\translationResource
     */
    public function lang_content()
    {
        return $this->translateResource('Dashboard/dataEntryTranslationFile');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(dataEntryRequest $request)
    {
        $commercial_name = $this->sanitizeString($request->commercial_name);
        $scientefic_name = $this->sanitizeString($request->scientefic_name);
        $provider = $this->sanitizeString($request->provider);
        $purchase_price = $this->setPercisionForFloatString($request->purchase_price);
        $selling_price = $this->setPercisionForFloatString($request->selling_price);
        $bonus = $this->setPercisionForFloatString($request->bonus);
        $concentrate = $this->setPercisionForFloatString($request->concentrate);

        // Check if either commercial name or scientefic_name exists
        $com_exists = false;
        $sc_exists = false;
        if (DataEntry::where('com_name', $commercial_name)->first(['id'])) {
            $com_exists = true;
        }
        if (DataEntry::where('sc_name', $scientefic_name)->first(['id'])) {
            $sc_exists = true;
        }
        if (!$com_exists && !$sc_exists) {
            /* Make the barcode for the product */
            // Generate A Barcode for the product
            $random_number = rand(1, 1000000000);
            while (file_exists(asset('storage/data_entry/'.$random_number.'.svg'))) {
                $random_number = rand(1, 1000000000);
            }
            // Store the barcode
            $barcode_value = $this->encrptString($random_number);
            if ($this->storeBarCodeSVG('data_entry', $random_number, $barcode_value)) {
                $data_entry = DataEntry::create([
                    'com_name' => $commercial_name,
                    'sc_name' => $scientefic_name,
                    'qty' => $request->quantity,
                    'pur_price' => $purchase_price,
                    'sel_price' => $selling_price,
                    'bonus' => $bonus,
                    'con' => $concentrate,
                    'patch_number' => $request->patch_number,
                    'bar_code' => $barcode_value,
                    'provider' => $provider,
                    'limited' => $request->limited ? 1 : 0,
                    'entry_date' => $request->entry_date,
                    'expire_date' => $request->expire_date,
                ]);

                return $this->success(new dataEntryResource($data_entry), 'Product Created Successfully');
            }

            // Failed To Create Or Store the barcode
            return $this->error(null, 500, 'Failed To Create Barcode');
        }

        // Either commercial Name or scientefic_name exists

        $payload = [];
        if ($com_exists) {
            $payload['commercial_name'] = [$this->translateErrorMessage($this->translationFileName.'commercial_name', 'unique')];
        }
        if ($sc_exists) {
            $payload['scientefic_name'] = [$this->translateErrorMessage($this->translationFileName.'scientefic_name', 'unique')];
        }

        return $this->validation_errors($payload);
    }

    /**
     * Summary of show.
     *
     * @return array
     */
    public function show(DataEntry $dataEntry)
    {
        return $this->resourceResponse(new dataEntryResource($dataEntry));
    }

    /**
     * Summary of update.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(dataEntryRequest $request, DataEntry $dataEntry)
    {
        $commercial_name = $this->sanitizeString($request->commercial_name);
        $scientefic_name = $this->sanitizeString($request->scientefic_name);
        $provider = $this->sanitizeString($request->provider);
        $purchase_price = $this->setPercisionForFloatString($request->purchase_price);
        $selling_price = $this->setPercisionForFloatString($request->selling_price);
        $bonus = $this->setPercisionForFloatString($request->bonus);
        $concentrate = $this->setPercisionForFloatString($request->concentrate);

        // Check if either commercial name or scientefic_name exists
        $com_exists = false;
        $sc_exists = false;
        if (DataEntry::where('com_name', $commercial_name)->where('id', '!=', $dataEntry->id)->first(['id'])) {
            $com_exists = true;
        }
        if (DataEntry::where('sc_name', $scientefic_name)->where('id', '!=', $dataEntry->id)->first(['id'])) {
            $sc_exists = true;
        }
        if (!$com_exists && !$sc_exists) {
            $random_number = null;
            $barCodeStored = false;
            $barCodeValue = null;
            $anyChangeOccured = false;
            // Check if $generate_another_bar_code Variable isset to generate another barcode
            if ($request->has('generate_another_bar_code') && $request->input('generate_another_bar_code') == true) {
                // Delete The Old Barcode
                $this->deleteBarCode($dataEntry->bar_code);

                // Generate A Barcode for the product
                $random_number = rand(1, 1000000000);
                while (file_exists(asset('storage/data_entry/'.$random_number.'.svg'))) {
                    $random_number = rand(1, 1000000000);
                }
                // Store the barcode
                $barCodeValue = $this->encrptString($random_number);
                $barCodeStored = $this->storeBarCodeSVG('data_entry', $random_number, $barCodeValue);
                $anyChangeOccured = true;
            }

            // Begin Update Logic If Any Change Occured
            if ($dataEntry->com_name != $commercial_name) {
                $dataEntry->com_name = $commercial_name;
                $anyChangeOccured = true;
            }
            if ($dataEntry->sc_name != $scientefic_name) {
                $dataEntry->sc_name = $scientefic_name;
                $anyChangeOccured = true;
            }
            if ($dataEntry->qty != $request->quantity) {
                $dataEntry->qty = $request->quantity;
                $anyChangeOccured = true;
            }
            if ($dataEntry->pur_price != $purchase_price) {
                $dataEntry->pur_price = $purchase_price;
                $anyChangeOccured = true;
            }
            if ($dataEntry->sel_price != $selling_price) {
                $dataEntry->sel_price = $selling_price;
                $anyChangeOccured = true;
            }
            if ($dataEntry->bonus != $bonus) {
                $dataEntry->bonus = $bonus;
                $anyChangeOccured = true;
            }
            if ($dataEntry->con != $concentrate) {
                $dataEntry->con = $concentrate;
                $anyChangeOccured = true;
            }
            if ($dataEntry->patch_number != $request->patch_number) {
                $dataEntry->patch_number = $request->patch_number;
                $anyChangeOccured = true;
            }
            if ($dataEntry->provider != $provider) {
                $dataEntry->provider = $provider;
                $anyChangeOccured = true;
            }
            if ($dataEntry->limited != (int) $request->limited) {
                $dataEntry->limited = $request->limited ? 1 : 0;
                $anyChangeOccured = true;
            }
            if ($this->changeDateFormat($dataEntry->entry_date, 'Y-m-d') != $request->entry_date) {
                $dataEntry->entry_date = $request->entry_date;
                $anyChangeOccured = true;
            }
            if ($this->changeDateFormat($dataEntry->expire_date, 'Y-m-d') != $request->expire_date) {
                $dataEntry->expire_date = $request->expire_date;
                $anyChangeOccured = true;
            }
            if (($random_number && $barCodeStored) || !$random_number) {
                if ($random_number) {
                    $dataEntry->bar_code = $barCodeValue;
                    $anyChangeOccured = true;
                }
                if ($anyChangeOccured) {
                    $dataEntry->update();

                    return $this->success(new dataEntryResource($dataEntry), 'Product Updated Successfully');
                }

                return $this->noContentResponse();
            }
            // Failed To Create Or Store the barcode
            return $this->error(null, 500, 'Failed To Create Barcode');
        }

        // Either commercial Name or scientefic_name exists

        $payload = [];
        if ($com_exists) {
            $payload['commercial_name'] = [$this->translateErrorMessage($this->translationFileName.'commercial_name', 'unique')];
        }
        if ($sc_exists) {
            $payload['scientefic_name'] = [$this->translateErrorMessage($this->translationFileName.'scientefic_name', 'unique')];
        }

        return $this->validation_errors($payload);
    }

    /**
     * Delete A Product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DataEntry $dataEntry)
    {
        $this->deleteBarCode($dataEntry->bar_code);
        $dataEntry->delete();

        return $this->success(null, 'Product Deleted Successfully');
    }
}
