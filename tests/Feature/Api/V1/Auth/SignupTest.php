<?php

namespace Api\V1\Auth;

use App\Models\User;
use App\Traits\TestingTrait;
use App\Traits\Translatable;
use Illuminate\Http\Response;
use Tests\TestCase;

class SignupTest extends TestCase
{
    use Translatable;
    use TestingTrait;
    private string $signupPath = 'Auth/Signup';

    public function testSignupWithNoPayload()
    {
        $response = $this->postJson(route('v1-signup'));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'data' => [
                'username',
                'password',
                'role',
                'phone',
            ]
        ]);
        $this->writeAFileForTesting($this->signupPath, 'All Values Required', $response->content());
    }

    public function testPassingNumberAsUsername()
    {
        $response = $this->postJson(route('v1-signup'), $this->getSignUpData('username', '112'));
        // $response->assertJsonStru([
        //     'username' => [$this->translateErrorMessage('username', 'username.regex')],
        // ]);
        $response->assertJsonStructure([
            'data' => [
                'username'
            ]
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->writeAFileForTesting($this->signupPath, 'UsernameCannotBeNumbersOnly', $response->content());
    }

    public function testPassingCharactersAndSymbolsForUsername()
    {
        $response = $this->postJson(route('v1-signup'), $this->getSignUpData('username', 'Aasd(*&*^#@$'));
        $response->assertJsonStructure([
            'data' => [
                'username'
            ]
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->writeAFileForTesting($this->signupPath, 'PassingCharactersAndSymbolsForUsername', $response->content());
    }

    public function testPasswordRules()
    {
        $response = $this->postJson(route('v1-signup'), $this->getSignUpData('password', 'google'));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->writeAFileForTesting($this->signupPath, 'passwordRules', $response->content());
    }

    public function testWithRightData()
    {
        if ($user = User::where('username', 'Aa2302')->first('id')) {
            $user->delete();
        }
        $response = $this->postJson(route('v1-signup'), $this->getSignUpData());
        $response->assertStatus(Response::HTTP_CREATED);
        $this->writeAFileForTesting($this->signupPath, 'passing', $response->content());
    }

    public function testExistingUsername()
    {
        $response = $this->postJson(route('v1-signup'), $this->getSignUpData('username', 'Aa2302'));
        $response->assertJsonStructure([
            'data' => ['username'],
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->writeAFileForTesting($this->signupPath, 'passingExistingUsername', $response->content());
        if ($user = User::where('username', 'Aa2302')->first('id')) {
            $user->delete();
        }
    }
}
