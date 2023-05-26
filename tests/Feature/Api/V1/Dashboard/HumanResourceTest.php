<?php

namespace Tests\Feature\Api\V1\Dashboard;

use App\Models\User;
use App\Models\V1\HumanResource;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class HumanResourceTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;

    private string $path = 'Dashboard/HumanResource/';

    public function testLogin(array $credentials = ['username' => 'human_resource', 'password' => 'human_resource'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->setToken(json_decode($response->getContent())->data->token);
    }

    public function testGetAllUsers()
    {
        $response = $this->getJson(route('human_resource_all'), [
            'Authorization' => 'Bearer '.$this->getToken(),
        ]);
        $response->assertSuccessful();
    }

    public function testGetOneUser()
    {
        $humanResource = HumanResource::create([
            'user_id' => User::where('username', 'data_entry')->value('id'),
            'status' => '1',
            'date' => now(),
            'attendance' => date('H:i'),
            'departure' => date('H:i'),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->getJson(route('human_resource_one', ['humanResource' => $humanResource->id]));
        $this->writeAFileForTesting($this->path, 'GetOneUser', $response->getContent());

        $response->assertSuccessful();
    }

    public function testStoreOrUpdateHumanResource()
    {
        $humanResource = HumanResource::create([
            'user_id' => User::where('username', 'data_entry')->value('id'),
            'status' => '1',
            'date' => now(),
            'attendance' => date('H:i'),
            'departure' => date('H:i'),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->putJson(route('human_resource_store_update'), json_decode('{
            "user_id" : "5",
            "status" : 0,
            "attendance":"08:30",
            "departure":"11:08",
            "date" : "1992-01-26"

        }', true));

        $this->writeAFileForTesting($this->path, 'StoreUser', $response->getContent());

        $response->assertSuccessful();
        $response->assertJsonStructure(['data' => [
            'id',
            'user_id',
            'attendance',
            'departure',
            'date',
            'status',
            'status_code',
            'full_name',
            'role_name',
        ]]);
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->postJson(route('human_resource_store_update'), json_decode('{
                "user_id" : "5",
                "status" : 0,
                "attendance":"08:30",
                "departure":"11:08",
                "date" : "1992-01-26"

            }', true));

        $response->assertSuccessful();
        $response->assertJsonStructure(['data' => [
            'id',
            'user_id',
            'attendance',
            'departure',
            'date',
            'status',
            'status_code',
            'full_name',
            'role_name',
        ]]);
    }
}
