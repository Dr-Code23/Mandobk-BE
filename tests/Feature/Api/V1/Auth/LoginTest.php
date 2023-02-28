<?php

namespace Api\V1\Auth;

use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use FileOperationTrait;
    use TestingTrait;
    private string $authPath = 'Auth/Login/';

    public function testLoginWithEmptyCredentials()
    {
        $response = $this->postJson(route('v1-login'));
        if (config('test.store_response')) {
            $this->writeAFileForTesting($this->authPath, 'loginPost', $response->content());
        }
        $response->assertSee('Username Cannot Be Empty');
        $response->assertSee('Password Cannot Be Empty');
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLoginWithWrongCredentials()
    {
        $credentials = ['username' => '1', 'password' => 'Aa123@#$'];
        $response = $this->postJson(route('v1-login'), $credentials);
        if (config('test.store_response')) {
            $this->writeAFileForTesting($this->authPath, 'WrongCredentials', $response->content());
        }
        $response->assertStatus(ResponseAlias::HTTP_UNAUTHORIZED);
        $response->assertSee('Wrong Credentials');
    }

    public function testLoginWithRightCredentials()
    {
        $credentials = ['username' => 'doctor', 'password' => 'doctor'];
        $response = $this->postJson(route('v1-login'), $credentials);
        if (config('test.store_response')) {
            $this->writeAFileForTesting($this->authPath, 'LoggedInSuccessfully', $response->content());
        }
        $response->assertStatus(ResponseAlias::HTTP_OK);

        // Store The Token To use it operations want token
        $this->setToken(json_decode($response->getContent())->data->token);
    }
}
