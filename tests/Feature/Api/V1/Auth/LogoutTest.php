<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Traits\fileOperationTrait;
use App\Traits\TestingTrait;
use Illuminate\Http\Response;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use fileOperationTrait;
    use TestingTrait;

    public function testLogoutWithNoAuthenticatedUser()
    {
        $response = $this->postJson(route('v1-logout'));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testLogoutAuthenticatedUser()
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->getToken())->postJson(route('v1-logout'));
        $response->assertStatus(200);
        $this->writeAFileForTesting('Auth/', 'LogoutSuccess', $response->getContent());
    }
}
