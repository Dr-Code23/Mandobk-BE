<?php

namespace Tests\Feature\Api\V1\Sales;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SaleTest extends TestCase
{

    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(404);
    }
}
