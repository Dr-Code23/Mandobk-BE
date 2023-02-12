<?php

namespace Tests\Feature\Api\V1\Offers;

use Tests\TestCase;

class OfferTest extends TestCase
{
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
