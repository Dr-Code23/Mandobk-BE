<?php

// namespace Tests\Feature\Api\V1\Visitor;

// use App\Models\User;
// use App\Models\V1\DoctorVisit;
// use App\Models\V1\Product;
// use App\Models\V1\Role;
// use App\Models\V1\VisitorRecipe;
// use App\Traits\FileOperationTrait;
// use App\Traits\TestingTrait;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
// use Tests\TestCase;

// class VisitorTest extends TestCase
// {
//     use TestingTrait;
//     use FileOperationTrait;
//     private string $path = 'Visitor';

//     public function SimpleLogin(array $credentials)
//     {
//         $response = $this->postJson(route('mobile-login'), $credentials);
//         $response->assertSuccessful();
//         $this->setToken(json_decode($response->getContent())->data->token);
//     }

//     public function testGetAllVisitorRecipesForAllRandomNumbers()
//     {

//         $visitor = User::create([
//             'full_name' => fake()->name(),
//             'username' => fake()->userName(),
//             'password' => 'Ts1234$#@',
//             'role_id' => Role::where('name', 'visitor')->value('id'),
//             'status' => '1'
//         ]);

//         $this->SimpleLogin(['username' => $visitor->username, 'password' => 'Ts1234$#@']);
//         Product::create(json_decode('{
//             "com_name" : "GoossgsssleGoogsles",
//             "sc_name" : "Googssssdssfassdfssssles",
//             "pur_price":"200",
//             "sel_price":"20",
//             "bonus" : "1",
//             "user_id" : "8",
//             "con" : "2",
//             "barcode" : "23021977",
//             "limited" : "1",
//             "role_id" : "2"
//         }', true));
//         $visitorRecipe = VisitorRecipe::create([
//             'visitor_id' => $visitor->id,
//             'random_number' => $visitor->id,
//             'alias' => fake()->name(),
//             'details' => json_decode('"products": [
//                 {
//                     "scientific_name": "Dr. Pink Hamill MD",
//                     "commercial_name": "Gladys Ritchie",
//                     "concentrate": 302.5451,
//                     "taken": false
//                 }
//             ],
//             "doctor_name": "doctor"', true)
//         ]);

//         $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
//             ->getJson(route('visitor-all-recipes'));

//         $this->writeAFileForTesting($this->path, 'AllVisitorRecipes', $response->getContent());
//         $response->assertSuccessful();
//         $response->assertJsonStructure([
//             'data' => [
//                 '*' => [
//                     'alias',
//                     'random_number',
//                     'details' => [
//                         'products' => [
//                             '*' => [
//                                 'scientific_name',
//                                 'commercial_name',
//                                 'concentrate',
//                                 'taken'
//                             ]
//                         ],
//                         'doctor_name'
//                     ]
//                 ]
//             ]
//         ]);
//     }

//     public function testGetAllItemsInArchive()
//     {
//         $visitor = User::create([
//             'full_name' => fake()->name(),
//             'username' => fake()->userName(),
//             'password' => fake()->name(),
//             'role_id' => Role::where('name', 'visitor')->value('id'),
//             'status' => '1'

//         ]);

//         $this->SimpleLogin(['username' => $visitor->username, 'password' => 'Ts1234$#@']);

//         Product::create(json_decode('{
//             "com_name" : "GoossgsssleGoogsles",
//             "sc_name" : "Googssssdssfassdfssssles",
//             "pur_price":"200",
//             "sel_price":"20",
//             "bonus" : "1",
//             "user_id" : "8",
//             "con" : "2",
//             "barcode" : "23021977",
//             "limited" : "1",
//             "role_id" : "2"
//         }', true));
//         $visitorRecipe = VisitorRecipe::create([
//             'visitor_id' => $visitor->id,
//             'random_number' => $visitor->id,
//             'alias' => fake()->name(),
//             'details' => json_decode('"products": [
//                 {
//                     "scientific_name": "Dr. Pink Hamill MD",
//                     "commercial_name": "Gladys Ritchie",
//                     "concentrate": 302.5451,
//                     "taken": false
//                 }
//             ],
//             "doctor_name": "doctor"', true)
//         ]);

//         DoctorVisit::create([
//             'visitor_recipe_id' => $visitorRecipe->id,
//             'doctor_id' => '7'
//         ]);


//         $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
//             ->getJson(route('visitor-all-recipes'));

//         $response->assertSuccessful();
//         $response->assertJsonStructure([
//             'data' => [
//                 '*' => [
//                     'alias',
//                     'random_number',
//                     'details' => [
//                         'products' => [
//                             '*' => [
//                                 'scientific_name',
//                                 'commercial_name',
//                                 'concentrate',
//                                 'taken'
//                             ]
//                         ],
//                         'doctor_name'
//                     ]
//                 ]
//             ]
//         ]);
//     }
// }
