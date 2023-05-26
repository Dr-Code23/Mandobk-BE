<?php

namespace Tests\Feature\Api\V1\Users;

use App\Models\User;
use App\Models\V1\Role;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use App\Traits\UserTrait;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class UserTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;
    use UserTrait;

    private string $path = 'Users/';

    public function testLogin(array $credentials = ['username' => 'ceo', 'password' => 'ceo'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertSuccessful();
        $this->setToken(json_decode($response->getContent())->data->token);
    }

    public function testGetAllUsersInDashboardToApprove()
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->getJson(route('dashboard-user-all'));
        $response->assertSuccessful();
        $this->writeAFileForTesting($this->path, 'GetAllUsersInDashboardToApprove', $response->getContent());
    }

    public function testchangeUserStatusWithWrongValue()
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->putJson(route('dashboard-user-updateSubUser', ['user' => '1']), ['status' => '3']);
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $this->writeAFileForTesting($this->path, 'changeUserStatusWithWrongValue', $response->getContent());
    }

    public function testChangeUserStatusWithRightValue()
    {
        foreach ([$this->isActive(), $this->isDeleted(), $this->isFrozen()] as $status) {
            $user = User::create([
                'full_name' => fake()->name(),
                'username' => fake()->name(),
                'password' => fake()->password(),
                'phone' => 'Google',
                'role_id' => Role::where('name', 'company')->value('id'),
            ]);
            $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
                ->putJson(route('dashboard-user-updateSubUser', ['user' => $user->id]), ['status' => $status]);
            $response->assertStatus(ResponseAlias::HTTP_OK);
            if ($status == $this->isDeleted()) {
                $response->assertJsonFragment([
                    'msg' => __('standard.deleted'),
                ]);
            } else {
                $response->assertJsonFragment([
                    'status' => $status,
                ]);
                $user->delete();
            }
        }
    }

    public function testGetAllUsersForSelect()
    {
        $this->login();
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->getJson(route('roles-storehouse-all'));
        $response->assertSuccessful();

        $this->login(['username' => 'storehouse', 'password' => 'storehouse']);
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->getJson(route('roles-pharmacy-all'));
        $response->assertSuccessful();

        $this->login(['username' => 'human_resource', 'password' => 'human_resource']);
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())
            ->getJson(route('roles-human_resource-all'));
        $response->assertSuccessful();
    }
}
