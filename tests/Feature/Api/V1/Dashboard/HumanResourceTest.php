<?php

namespace Tests\Feature\Api\V1\Dashboard;

use App\Models\User;
use App\Models\V1\HumanResource;
use App\Models\V1\Role;
use App\Traits\TestingTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class HumanResourceTest extends TestCase
{
    use TestingTrait;

    public function testLogin(array $credentials = ['username' => 'human_resource', 'password' => 'human_resource'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertStatus(Response::HTTP_OK);
        $this->setToken(json_decode($response->getContent())->data->token);
    }
    public function testGetAllUsers()
    {
        $response = $this->getJson(route('human_resource_all'), [
            'Authorization' => 'Bearer ' . $this->getToken()
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

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('human_resource_one', ['user' => $humanResource->user_id]));

        $response->assertSuccessful();
    }
}
