<?php

namespace Tests\Feature\Api\V1\Dashboard;

use App\Models\User;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class MonitorTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;

    private string $path = 'Dashboard/Monitor/';
    public function testLogin(array $credentials = ['username' => 'monitor_and_evaluation', 'password' => 'monitor_and_evaluation'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertSuccessful();
        $this->setToken(json_decode($response->getContent())->data->token);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetAllUsers()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('monitor-all'));
        $this->writeAFileForTesting($this->path, 'AllUsers', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'full_name',
                    'username',
                    'role_id',
                    'role_name'
                ]
            ]
        ]);
    }

    public function testGetOneUser()
    {
        $user = User::create([
            'role_id' => 3,
            'username' => fake()->name(),
            'password' => 'Usernassmes',
            'full_name' => fake()->name()

        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('monitor-one', ['user' => $user->id]));
        $this->writeAFileForTesting($this->path, 'OneUser', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'full_name',
                'username',
                'role_id',
                'role_name'
            ]
        ]);
    }

    public function testStoreUser()
    {
        User::where('username', 'Usernassmes')->delete();
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('monitor-storeSubUser'), json_decode('{
                "full_name" : "Ahmed Mohamed",
                "username": "Usernassmes",
                "password" : "Password2302@",
                "role" : "3"
            }', true));
        $this->writeAFileForTesting($this->path, 'StoreUser', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'full_name',
                'username',
                'role_id',
                'role_name'
            ]
        ]);
        User::where('username', 'Usernassmes')->delete();
    }

    public function testUpdateUser()
    {
        $user = User::create([
            'role_id' => 3,
            'username' => fake()->name(),
            'password' => 'Usernassmes',
            'full_name' => fake()->name()

        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->putJson(route('monitor-updateSubUser', ['user' =>  $user->id]), json_decode('{
                "full_name" : "Ahmed Mohamed",
                "username": "Usernassmes",
                "password" : "Password2302@",
                "role" : "3"
            }', true));
        $this->writeAFileForTesting($this->path, 'UpdateUser', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'full_name',
                'username',
                'role_id',
                'role_name'
            ]
        ]);
    }

    public function testDeleteUser()
    {
        $user = User::create([
            'role_id' => 3,
            'username' => fake()->name(),
            'password' => 'Usernassmes',
            'full_name' => fake()->name()

        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->deleteJson(route('monitor-delete', ['user' => $user->id]));
        $this->writeAFileForTesting($this->path, 'DeleteUser', $response->getContent());
        $response->assertSuccessful();
    }
}
