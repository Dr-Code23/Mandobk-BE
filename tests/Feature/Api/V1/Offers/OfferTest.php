<?php

namespace Tests\Feature\Api\V1\Offers;

use App\Models\V1\Offer;
use App\Models\V1\Product;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use Illuminate\Http\Response as HttpResponse;
use Response;
use Tests\TestCase;

class OfferTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;
    private string $path = 'Offers';

    public function testLogin(array $credentials = ['username' => 'company', 'password' => 'company'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertStatus(HttpResponse::HTTP_OK);
        $this->setToken(json_decode($response->getContent())->data->token);
    }
    public function testGetAllOffers()
    {
        $this->testLogin();
        $this->login(['username' => 'company', 'password' => 'company']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('offer-all'));
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'product_id',
                    'start_date',
                    'end_date',
                    'pay_method_id',
                    'status',
                ]
            ],
            'msg',
            'code'
        ]);
    }

    public function testGetOneOffer()
    {
        $this->testLogin();

        $offer = Offer::where('user_id', '8')
            ->where('type', '1')
            ->first();
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('offer-one', ['offer' => $offer->id]));

        $this->writeAFileForTesting($this->path, 'GetOneOffer', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'product_id',
                'start_date',
                'end_date',
                'pay_method_id',
                'status',
            ],
            'msg',
            'code'
        ]);
    }

    public function testStoreOffer()
    {
        $this->testLogin();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('offer-store'), $this->getOfferData('product_id', Product::where('user_id', '8')->value('id')));
        $this->writeAFileForTesting($this->path, 'StoreOffer', $response->getContent());
        $response->assertCreated();
    }

    public function testUpdateOffer()
    {
        $offer = Offer::where('user_id', '8')->first();
        info($offer);
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->putJson(route('offer-status', ['offer' => $offer->id]), ['status' => '1']);
        $this->writeAFileForTesting($this->path, 'UpdateOffer', $response->getContent());
        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'product_id',
                'start_date',
                'end_date',
                'pay_method_id',
                'status',
            ],
            'msg',
            'code'
        ]);
    }

    public function testDeleteOffer()
    {
        $this->testLogin();
        $offer = Offer::where('user_id', '8')->first();
        info($offer);
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->deleteJson(route('offer-delete', ['offer' => $offer->id]));
        $this->writeAFileForTesting($this->path, 'DeleteOffer', $response->getContent());
        $response->assertSuccessful();
    }
}
