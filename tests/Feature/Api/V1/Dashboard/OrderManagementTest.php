<?php

namespace Tests\Feature\Api\V1\Dashboard;

use App\Models\V1\Offer;
use App\Models\V1\OfferOrder;
use App\Models\V1\Product;
use App\Models\V1\ProductInfo;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;

    private string $path = 'Dashboard/OrderManagement/';
    public function testLogin(array $credentials = ['username' => 'order_management', 'password' => 'order_management'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertSuccessful();
        $this->setToken(json_decode($response->getContent())->data->token);
    }

    public function testGetAllOrders()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('management-all'));
        $this->writeAFileForTesting($this->path, 'AllOrders', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'commercial_name',
                    'offer_from_name',
                    'offer_to_name',
                    'purchase_price',
                    'quantity',
                    'status',
                    'status_code',
                    'created_at'
                ]
            ]
        ]);
    }

    public function testChangeOrderStatus()
    {

        $product = Product::create(json_decode('{
            "com_name" : "GoossgsssleGoogsles",
            "sc_name" : "Googssssdssfassdfssssles",
            "pur_price":"200",
            "sel_price":"20",
            "bonus" : "1",
            "user_id" : "8",
            "con" : "2",
            "barcode" : "23021977",
            "limited" : "1",
            "role_id" : "8"
        }', true));

        ProductInfo::create([
            'role_id' => '8',
            'product_id' => $product->id,
            'qty' => 1000,
            'expire_date' => now(),
            'patch_number' => '1000',

        ]);
        $offer = Offer::create([
            'user_id' => '8',
            'product_id' => $product->id,
            'from' => now(),
            'to' => now(),
            'date' => date('Y-m-d'),
            'type' => '1',
            'status' => '1',
            'pay_method' => '1'
        ]);


        // # Make Order
        $order = OfferOrder::create([
            'offer_id' => $offer->id,
            'want_offer_id' => '11',
            'qty' => 1,
            'status' => '1'
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('management-accept', ['order' => $order->id]), ['approve' => '2']);
        $this->writeAFileForTesting($this->path, 'ChangeOrderStatus', $response->getContent());
        $response->assertSuccessful();
    }
}
