<?php

namespace Tests\Feature\Api\V1\Products;

use App\Traits\fileOperationTrait;
use App\Traits\TestingTrait;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use TestingTrait;
    use fileOperationTrait;
    private string $path = 'Products/';

    public function testLogin(array $credentials = ['username' => 'company', 'password' => 'company'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertStatus(Response::HTTP_OK);
        $this->setToken(json_decode($response->getContent())->data->token);
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
