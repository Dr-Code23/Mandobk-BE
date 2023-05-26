<?php

namespace Tests\Feature\Api\V1\OfferOrder;

use App\Models\V1\Offer;
use App\Models\V1\Product;
use App\Models\V1\ProductInfo;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use Tests\TestCase;

class OfferOrderTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;

    private string $path = 'OfferOrder/';

    public function testLogin(array $credentials = ['username' => 'storehouse', 'password' => 'storehouse'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertSuccessful();
        $this->setToken(json_decode($response->getContent())->data->token);
    }

    public function testGetAllOffersToOrder()
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->getJson(route('order-company-showOneSubUser'));
        $this->writeAFileForTesting($this->path, 'GetAllOffers', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'date_from',
                    'date_to',
                    'product' => [
                        'commercial_name',
                        'scientific_name',
                        'concentrate',
                    ],
                ],
            ],
        ]);
    }

    public function testMakeOrder()
    {
        $product = Product::create(json_decode('{
            "com_name" : "GoossgsssleGoogsles",
            "sc_name" : "Googssssdssfassdfssssles",
            "pur_price":"200",
            "sel_price":"20",
            "bonus" : "1",
            "user_id" : "8",
            "con" : "2",
            "barcode" : "23021977",
            "limited" : "1",
            "role_id" : "8"
        }', true));

        ProductInfo::create([
            'role_id' => '8',
            'product_id' => $product->id,
            'qty' => 1000,
            'expire_date' => now(),
            'patch_number' => '1000',

        ]);
        $offer = Offer::create([
            'user_id' => '8',
            'product_id' => $product->id,
            'from' => now(),
            'to' => now(),
            'date' => date('Y-m-d'),
            'type' => '1',
            'status' => '1',
            'pay_method' => '1',
        ]);

        info($offer);
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->postJson(route('order-company-make', [
                'offer_id' => $offer->id,
                'quantity' => 50,
            ]));
        $this->writeAFileForTesting($this->path, 'MakeOrder', $response->getContent());
        $response->assertSuccessful();
    }
}
