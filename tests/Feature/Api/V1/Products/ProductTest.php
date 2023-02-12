<?php

namespace Tests\Feature\Api\V1\Products;

use App\Traits\fileOperationTrait;
use App\Traits\TestingTrait;
use App\Traits\translationTrait;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use TestingTrait;
    use fileOperationTrait;
    use translationTrait;
    private string $path = 'Products/';

    public function testLogin(array $credentials = ['username' => 'visitor', 'password' => 'visitor'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertStatus(Response::HTTP_OK);
        $this->setToken(json_decode($response->getContent())->data->token);
    }

    public function testStoreProduct()
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->postJson(route('v1-products-store'));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment([
            'commercial_name' => $this->translateErrorMessage('commercial_name', 'required'),
            'scientific_name' => $this->translateErrorMessage('scientific_name', 'required'),
            'quantity' => $this->translateErrorMessage('quantity', 'required'),
            'concentrate' => $this->translateErrorMessage('concentrate', 'required'),
            'bonus' => $this->translateErrorMessage('bonus', 'required'),
            'selling_price' => $this->translateErrorMessage('selling_price', 'required'),
            'purchase_price' => $this->translateErrorMessage('purchase_price', 'required'),
            'patch_number' => $this->translateErrorMessage('patch_number', 'required'),
            'expire_date' => $this->translateErrorMessage('expire_date', 'required'),
            'provider' => $this->translateErrorMessage('provider', 'required'),
            'barcode' => $this->translateErrorMessage('barcode', 'required'),
        ]);
        dd($response->getContent());
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetAllProducts()
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->getJson(route('v1-products-all'));
        $response->assertStatus(Response::HTTP_OK);

        $this->writeAFileForTesting($this->path, 'GetAllProducts', $response->getContent());
    }
}
