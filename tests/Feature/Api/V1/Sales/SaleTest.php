<?php

namespace Tests\Feature\Api\V1\Sales;

use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;
    private string $path = 'Sales/';

    public function testLogin(array $credentials = ['username' => 'company', 'password' => 'company'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertStatus(Response::HTTP_OK);
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
    public function testStoreSale()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('sales-store'), json_decode('
            {
                "data":[
                    {
                        "product_id" : "1",
                        "expire_date":"2020-12-11",
                        "selling_price" : "1",
                        "quantity" : "1"
                    }
                ],
                "buyer_id" : "11"
            }
            ', true));

        $this->writeAFileForTesting($this->path, 'StoreSale', $response->getContent());

        $response->assertSuccessful();

        //! Laravel assertJsonStructure Have Problems
        $response->assertSee([
            'selling_price',
            'quantity',
            'commercial_name',
            'scientific_name',
            'purchase_price',
        ]);
    }
}
