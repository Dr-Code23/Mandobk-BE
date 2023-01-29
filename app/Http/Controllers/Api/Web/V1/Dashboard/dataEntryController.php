<?php

namespace App\Http\Controllers\Api\Web\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Dashboard\dataEntryRequest;
use App\Http\Resources\Api\Web\V1\Dashboard\DataEntry\dataEntryCollection;
use App\Http\Resources\Api\Web\V1\Dashboard\DataEntry\dataEntryResource;
use App\Models\Api\Web\V1\DataEntry;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\translationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;

class dataEntryController extends Controller
{
    use translationTrait;
    use StringTrait;
    use HttpResponse;
    use translationTrait;

    private string $translationFileName = 'Dashboard/dataEntryTranslationFile.';

    public function index()
    {
        return $this->resourceResponse( new dataEntryCollection(DataEntry::all()));
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
            if ($this->storeBarCodeSVG('data_entry', $random_number)) {
                $data_entry = DataEntry::create([
                    'com_name' => $commercial_name,
                    'sc_name' => $scientefic_name,
                    'qty' => $request->quantity,
                    'pur_price' => $request->purchase_price,
                    'sel_price' => $request->selling_price,
                    'bonus' => $request->bonus,
                    'con' => $request->concentrate,
                    'patch_number' => $request->patch_number,
                    'bar_code' => $random_number,
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
     * Summary of show
     * @param DataEntry $dataEntry
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
    public function update(dataEntryRequest $request,DataEntry $dataEntry)
    {
        $commercial_name = $this->sanitizeString($request->commercial_name);
        $scientefic_name = $this->sanitizeString($request->scientefic_name);
        $provider = $this->sanitizeString($request->provider);

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
            // Check if $generate_another_bar_code Variable isset to generate another barcode
            if ($request->has('generate_another_bar_code')) {
                // Delete The Old Barcode
                $this->deleteBarCode($dataEntry->bar_code);

                // Generate A Barcode for the product
                $random_number = rand(1, 1000000000);
                while (file_exists(asset('storage/data_entry/'.$random_number.'.svg'))) {
                    $random_number = rand(1, 1000000000);
                }
                // Store the barcode
                $barCodeStored = $this->storeBarCodeSVG('data_entry', $random_number);
            }

            // Begin Update Logic
            $dataEntry->com_name = $commercial_name;
            $dataEntry->sc_name = $scientefic_name;
            $dataEntry->qty = $request->quantity;
            $dataEntry->pur_price = $request->purchase_price;
            $dataEntry->sel_price = $request->selling_price;
            $dataEntry->bonus = $request->bonus;
            $dataEntry->con = $request->concentrate;
            $dataEntry->patch_number = $request->patch_number;
            $dataEntry->provider = $provider;
            $dataEntry->limited = $request->limited ? 1 : 0;
            $dataEntry->entry_date = $request->entry_date;
            $dataEntry->expire_date = $request->expire_date;
            if (($random_number && $barCodeStored) || !$random_number) {
                if($random_number)$dataEntry->bar_code = $random_number;
                $dataEntry->update();
                return $this->success(new dataEntryResource($dataEntry), 'Product Updated Successfully');
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
     * Delete A Product
     * @param DataEntry $dataEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DataEntry $dataEntry)
    {
        $this->deleteBarCode($dataEntry->bar_code);
        $dataEntry->delete();
        return $this->success(null, 'Product Deleted Successfully');
    }
}
