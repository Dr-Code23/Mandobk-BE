<?php

namespace Tests\Feature\Api\V1\Products;

use App\Models\V1\Product;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use App\Traits\TranslationTrait;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;
    use TranslationTrait;
    private string $path = 'Products/';

    public function testLogin(array $credentials = ['username' => 'company', 'password' => 'company'])
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
            'commercial_name' => [$this->translateErrorMessage('commercial_name', 'required')],
            'scientific_name' => [$this->translateErrorMessage('scientific_name', 'required')],
            'quantity' => [$this->translateErrorMessage('quantity', 'required')],
            'concentrate' => [$this->translateErrorMessage('concentrate', 'required')],
            'bonus' => [$this->translateErrorMessage('bonus', 'required')],
            'selling_price' => [$this->translateErrorMessage('selling_price', 'required')],
            'purchase_price' => [$this->translateErrorMessage('purchase_price', 'required')],
            'patch_number' => [$this->translateErrorMessage('patch_number', 'required')],
            'expire_date' => [$this->translateErrorMessage('expire_date', 'required')],
            'provider' => [$this->translateErrorMessage('provider', 'required')],
            'barcode' => [$this->translateErrorMessage('barcode', 'required')],
        ]);

        $this->writeAFileForTesting($this->path, 'AllValuesAreRequired', $response->getContent());
    }

    public function testQuantity()
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->postJson(route('v1-products-store'), $this->getProductsData('quantity', 'Google'));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment([
            'quantity' => [$this->translateErrorMessage('quantity', 'quantity.regex')],
        ]);
        $this->writeAFileForTesting($this->path, 'QuantityInvalid', $response->getContent());
    }

    public function testStoreProductSuccessfully()
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->postJson(route('v1-products-store'), $this->getProductsData());
        $response->assertStatus(Response::HTTP_OK);
        Product::where('id', json_decode($response->getContent())->data->id)->delete();
        $this->writeAFileForTesting($this->path, 'StoreProductSuccessfully', $response->getContent());
    }

    public function testGetAllProducts()
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->getJson(route('v1-products-all'));
        $response->assertStatus(Response::HTTP_OK);

        $this->writeAFileForTesting($this->path, 'GetAllProducts', $response->getContent());
    }
}
