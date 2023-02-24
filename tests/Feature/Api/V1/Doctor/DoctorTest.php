<?php

namespace Tests\Feature\Api\V1\Doctor;

use App\Models\User;
use App\Models\V1\DoctorVisit;
use App\Models\V1\Product;
use App\Models\V1\Role;
use App\Models\V1\VisitorRecipe;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class DoctorTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;
    private string $path = 'Doctor/';

    public function testLogin(array $credentials = ['username' => 'doctor', 'password' => 'doctor'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertSuccessful();
        $this->setToken(json_decode($response->getContent())->data->token);
    }

    public function testGetAllRecipes()
    {

        $visitor = User::create([
            'full_name' => fake()->name(),
            'username' => fake()->userName(),
            'password' => fake()->name(),
            'role_id' => Role::where('name', 'visitor')->value('id')
        ]);

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
            "role_id" : "2"
        }', true));
        $visitorRecipe = VisitorRecipe::create([
            'visitor_id' => $visitor->id,
            'random_number' => $visitor->id,
            'alias' => fake()->name(),
            'details' => []
        ]);

        DoctorVisit::create([
            'visitor_recipe_id' => $visitorRecipe->id,
            'doctor_id' => '7'
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('doctor-recipe-all'));

        $this->writeAFileForTesting($this->path, 'GetAllDoctorRecipes', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'visitor_name',
                    'created_at'
                ]
            ]
        ]);
    }

    public function testMakeRecipeForVisitor()
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
            "role_id" : "2"
        }', true));

        $visitor = User::create([
            'full_name' => fake()->name(),
            'username' => fake()->userName(),
            'password' => fake()->name(),
            'role_id' => Role::where('name', 'visitor')->value('id')
        ]);

        $visitorRecipe = VisitorRecipe::create([
            'visitor_id' => $visitor->id,
            'random_number' => $visitor->id,
            'alias' => fake()->name(),
            'details' => []
        ]);


        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('doctor-recipe-add', [
                'products' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                    ]
                ],
                'random_number' => $visitorRecipe->random_number,
            ]));
        $this->writeAFileForTesting($this->path, 'MakeRecipeForVisitor', $response->getContent());
        $response->assertSuccessful();
    }


    public function testRegisterNewVisitor()
    {

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('doctor-visitor-register', [
                'name' => fake()->name(),
                'username' => 'Google' . rand(1, 10000),
                'password' => 'Aa13123!#!',
                'phone' => fake()->numberBetween(1, 100000000),
                'alias' => fake()->name()
            ]));
        $this->writeAFileForTesting($this->path, 'RegisterNewVisitor', $response->getContent());
        $response->assertSuccessful();
    }

    public function testGetAllProductsAssociatedWithRandomNumber()
    {
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
            "role_id" : "2"
        }', true));

        $visitor = User::create([
            'full_name' => fake()->name(),
            'username' => fake()->userName(),
            'password' => fake()->name(),
            'role_id' => Role::where('name', 'visitor')->value('id')
        ]);

        $visitorRecipe = VisitorRecipe::create([
            'visitor_id' => $visitor->id,
            'random_number' => $visitor->id,
            'alias' => fake()->name(),
            'details' => []
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('doctor-visitor-products', ['random_number' => $visitorRecipe->random_number]));
        $this->writeAFileForTesting($this->path, 'GetAllProductsWithRandomNumber', $response->getContent());
        $response->assertSuccessful();

        $response->assertJsonFragment([
            'data' => []
        ]);

        DoctorVisit::create([
            'visitor_recipe_id' => $visitorRecipe->id,
            'doctor_id' => '7'
        ]);
    }


    public function testRestoreRandomNumberForUser()
    {
        $visitor = User::create([
            'full_name' => fake()->name(),
            'username' => fake()->userName(),
            'password' => fake()->name(),
            'role_id' => Role::where('name', 'visitor')->value('id')
        ]);

        $visitorRecipe = VisitorRecipe::create([
            'visitor_id' => $visitor->id,
            'random_number' => $visitor->id,
            'alias' => fake()->name(),
            'details' => []
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('doctor-visitor-forgot-random-number', ['handle' => $visitor->username]));
        $this->writeAFileForTesting($this->path, 'RestoreRandomNumbersForVisitor', $response->getContent());
        $response->assertSuccessful();
        // ! Are U Kidden Me !
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'random_number',
                    'alias'
                ]
            ]
        ]);
        // $response->dd();
    }
}
