<?php

namespace Api\V1\Auth;

use App\Traits\fileOperationTrait;
use Illuminate\Http\Response;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use fileOperationTrait;
    private string $authPath = 'Auth/';

    public function testLoginWithEmptyCredentials()
    {
        $response = $this->postJson(route('v1-login'));
        $this->writeAFileForTesting($this->authPath, 'loginPost', $response->content());
        $response->assertSee('Username Cannot Be Empty');
        $response->assertSee('Password Cannot Be Empty');
        $response->assertStatus(422);
    }

    public function testLoginWithWrongCredentials()
    {
        $credentials = ['username' => '1', 'password' => 'Aa123@#$'];
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertSee('Wrong Credentials');
    }

    public function testLoginWithRightCredentials()
    {
        $credentials = ['username' => 'doctor', 'password' => 'doctor'];
        $response = $this->postJson(route('v1-login'), $credentials);
        $this->writeAFileForTesting($this->authPath, 'LoggedInSuccessfully', $response->content());
        $response->assertStatus(200);
    }
}
