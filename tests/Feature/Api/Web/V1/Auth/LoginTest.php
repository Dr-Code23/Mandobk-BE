<?php

namespace Tests\Feature\Api\Web\V1\Auth;

use App\Traits\fileOperationTrait;
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

    public function testValidationAllValuesRequired()
    {
        $response = $this->postJson(route('v1-user'));
        $this->writeAFileForTesting($this->authPath, 'requiredValues', $response->content());
        $response->assertStatus(422);
    }

    public function testLoginWithRightCredentials()
    {
        $credentials = ['username' => 'doctor', 'password' => 'doctor'];
        $response = $this->postJson(route('v1-user'), $credentials);
        $this->writeAFileForTesting($this->authPath, 'LoggedInSuccessfully', $response->content());
        $response->assertStatus(200);
    }
}
