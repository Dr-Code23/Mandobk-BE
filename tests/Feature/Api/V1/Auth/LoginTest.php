<?php

namespace Api\V1\Auth;

use App\Traits\fileOperationTrait;
use Illuminate\Http\Response;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use fileOperationTrait;
    private string $authPath = 'Auth/Login/';

    public function testLoginWithEmptyCredentials()
    {
        $response = $this->postJson(route('v1-login'));
        if (config('test.store_response')) {
            $this->writeAFileForTesting($this->authPath, 'loginPost', $response->content());
        }
        $response->assertSee('Username Cannot Be Empty');
        $response->assertSee('Password Cannot Be Empty');
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLoginWithWrongCredentials()
    {
        $credentials = ['username' => '1', 'password' => 'Aa123@#$'];
        $response = $this->postJson(route('v1-login'), $credentials);
        if (config('test.store_response')) {
            $this->writeAFileForTesting($this->authPath, 'WrongCredentials', $response->content());
        }
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertSee('Wrong Credentials');
    }

    public function testLoginWithRightCredentials()
    {
        $credentials = ['username' => 'doctor', 'password' => 'doctor'];
        $response = $this->postJson(route('v1-login'), $credentials);
        if (config('test.store_response')) {
            $this->writeAFileForTesting($this->authPath, 'LoggedInSuccessfully', $response->content());
        }
        $response->assertStatus(Response::HTTP_OK);
    }
}
