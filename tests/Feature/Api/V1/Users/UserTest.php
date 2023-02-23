<?php

namespace Tests\Feature\Api\V1\Users;

use App\Models\User;
use App\Models\V1\Role;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use App\Traits\UserTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Facade\Str;
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
        $response->assertStatus(Response::HTTP_OK);
        $this->setToken(json_decode($response->getContent())->data->token);
    }

    public function testGetAllUsersInDashboardToApprove()
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('dashboard-user-all'));
        $response->assertSuccessful();
        $this->writeAFileForTesting($this->path, 'GetAllUsersInDashboardToApprove', $response->getContent());
    }

    public function testchangeUserStatusWithWrongValue()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->putJson(route('dashboard-user-update', ['user' => '1']), ['status' => '3']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
                'role_id' => Role::where('name', 'company')->value('id')
            ]);
            $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
                ->putJson(route('dashboard-user-update', ['user' => $user->id]), ['status' => $status]);
            $response->assertStatus(Response::HTTP_OK);
            if ($status == $this->isDeleted()) $response->assertJsonFragment([
                'msg' => __('standard.deleted')
            ]);
            else {
                $response->assertJsonFragment([
                    'status' => $status
                ]);
                $user->delete();
            }
        }
    }
}
