<?php

namespace Tests\Feature\Api\V1\Products;

use App\Models\V1\Product;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use App\Traits\Translatable;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;
    use Translatable;
    private string $path = 'Products/';

    public function testLogin(array $credentials = ['username' => 'company', 'password' => 'company'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertStatus(Response::HTTP_OK);
        $this->setToken(json_decode($response->getContent())->data->token);
    }

    public function testStoreProduct()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
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
            'barcode' => [$this->translateErrorMessage('barcode', 'required')],
        ]);

        $this->writeAFileForTesting($this->path, 'AllValuesAreRequired', $response->getContent());
    }

    public function testQuantity()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('v1-products-store'), $this->getProductsData('quantity', 'Google'));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment([
            'quantity' => [$this->translateErrorMessage('quantity', 'quantity.regex')],
        ]);
        $this->writeAFileForTesting($this->path, 'QuantityInvalid', $response->getContent());
    }

    public function testStoreProductSuccessfully()
    {
        if ($product = Product::where('com_name', 'TestCommercialName')->first('id')) {
            $product->delete();
        }
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('v1-products-store'), $this->getProductsData());
        $this->writeAFileForTesting($this->path, 'StoreProductSuccessfully', $response->getContent());
        $response->assertStatus(Response::HTTP_OK);
        Product::where('id', json_decode($response->getContent())->data->id)->delete();
    }

    public function testGetAllProducts()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('v1-products-all'));
        $response->assertStatus(Response::HTTP_OK);

        $this->writeAFileForTesting($this->path, 'GetAllProducts', $response->getContent());
    }

    public function testGetAllScientificNames()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('v1-products-scientific'));
        $this->writeAFileForTesting($this->path, 'GetAllScientificNames', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'scientific_name',
                ],
            ],
        ]);
    }
    public function testGetAllCommercialNames()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('v1-products-commercial'));
        $this->writeAFileForTesting($this->path, 'GetAllCommercialNames', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'commercial_name',
                ],
            ],
        ]);
    }

    public function testGetOneProductWithNoDetails()
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
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('v1-products-one', ['product' => $product->id]));
        $this->writeAFileForTesting($this->path, 'GetOneProductWithNoDetails', $response->getContent());
        $response->assertSuccessful();

        $content = json_decode($response->getContent(), true);
        $response->assertSee([
            'id',
            'commercial_name',
            'scientific_name',
            'commercial_name',
            'bonus',
            'concentrate',
            'limited',
            'purchase_price',
            'selling_price',
            'barcode',
        ]);
    }

    public function testGetProductsForDoctor()
    {
        $this->login(['username' => 'doctor', 'password' => 'doctor']);
        Product::create(json_decode('{
            "com_name" : "GoossgsssleGoogsles",
            "sc_name" : "Googssssdssfassdfssssles",
            "pur_price":"200",
            "sel_price":"20",
            "bonus" : "1",
            "user_id" : "8",
            "con" : "2",
            "barcode" : "23021977",
            "limited" : "1",
            "role_id" : "1"
        }', true));


        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('v1-products-doctor'));
        $this->writeAFileForTesting($this->path, 'GetProductsForDoctor', $response->getContent());
        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'commercial_name', 'limited']
            ]
        ]);
    }
}
