<?php

namespace Tests\Feature\Api\Web\V1\Auth;

use App\Traits\fileOperationTrait;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use fileOperationTrait;
    private string $authPath = 'Auth/';

    public function testLoginProcessPageIsWorking()
    {
        $res = $this->postJson(route('web-v1-login-user'));
        $this->writeAFileForTesting($this->authPath, 'loginPost', $res->content());
        $res->assertStatus(422);
    }

    public function testValidationAllValuesRequired()
    {
        $res = $this->postJson(route('web-v1-login-user'));
        $this->writeAFileForTesting($this->authPath, 'requiredValues', $res->content());
        $res->assertStatus(422);
        $res->assertSeeText('Username Cannot Be Empty');
        $res->assertSeeText('Password Cannot Be Empty');
    }

    public function testLoginWithRightCredentials()
    {
        $credentials = ['username' => 'doctor', 'password' => 'doctor'];
        $res = $this->postJson(route('web-v1-login-user'), $credentials);
        $this->writeAFileForTesting($this->authPath, 'LoggedInSuccessfully', $res->content());
        $res->assertStatus(200);
    }
}
