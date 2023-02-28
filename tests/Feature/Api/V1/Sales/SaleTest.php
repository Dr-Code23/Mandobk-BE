<?php

namespace Tests\Feature\Api\V1\Sales;

use App\Models\V1\Product;
use App\Models\V1\ProductInfo;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;
    private string $path = 'Sales/';

    public function testLogin(array $credentials = ['username' => 'company', 'password' => 'company'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->setToken(json_decode($response->getContent())->data->token);
    }

    public function testGetAllSales()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('sales-all'));
        $this->writeAFileForTesting($this->path, 'GetAllSales', $response->content());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'full_name', 'details' => [
                        '*' => [
                            'selling_price',
                            'quantity',
                            'commercial_name',
                            'scientific_name',
                            'purchase_price',
                        ]
                    ],
                    'created_at'
                ],
            ],
            'msg',
            'type'
        ]);
    }
    // public function testStoreSale()
    // {

    //     $this->login(['username' => 'company', 'password' => 'company']);
    //     $product = Product::create([
    //         'com_name' => 'Google',
    //         'sc_name' => 'Google',
    //         'pur_price' => 100,
    //         'sel_price' => 100,
    //         'bonus' => 100,
    //         'user_id' => 8,
    //         'con' => 9,
    //         'barcode' => 2302197,
    //         'role_id' => 8
    //     ]);

    //     ProductInfo::create([
    //         'role_id' => '8',
    //         'product_id' => $product->id,
    //         'qty' => 1000,
    //         'expire_date' => now(),
    //         'patch_number' => '1000',

    //     ]);
    //     $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
    //         ->postJson(
    //             route('sales-storeSubUser'),
    //             [
    //                 'data' => [
    //                     'product_id' => $product->id,
    //                     'selling_price' => 100,
    //                     "quantity" => 50,
    //                 ],
    //                 'buyer_id' => 11
    //             ]
    //         );

    //     $response->dd();
    //     $this->writeAFileForTesting($this->path, 'StoreSale', $response->getContent());

    //     $response->assertSuccessful();

    //     //! Laravel assertJsonStructure Have Problems
    //     // $response->assertSee([
    //     //     'selling_price',
    //     //     'quantity',
    //     //     'commercial_name',
    //     //     'scientific_name',
    //     //     'purchase_price',
    //     // ]);
    // }
}
