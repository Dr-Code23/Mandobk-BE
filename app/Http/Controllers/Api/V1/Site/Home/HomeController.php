<?php

namespace App\Http\Controllers\Api\V1\Site\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Product\productCollection;
use App\Models\V1\Product;
use App\Models\V1\Role;
use App\Models\V1\Sale;
use App\Traits\StringTrait;
use App\Traits\userTrait;

class HomeController extends Controller
{
    use userTrait;
    use StringTrait;

    public function index()
    {
        // Purchase Price

        $total_purchases = $total_sales =
            $daily_purchases = $daily_sales =
            $monthly_purchases = $monthly_sales = 0;
        // Purchases
        foreach (Product::whereIn('user_id', $this->getSubUsersForAuthenticatedUser())
            ->get(['pur_price', 'qty', 'created_at']) as $product) {
            // product info
            $purchase = ($product->pur_price * $product->qty);
            $created_at = date('Y-m-d', strtotime($product->created_at));

            $total_purchases += $purchase;
            // Find Daily Purchases
            if (date('Y-m-d') == $created_at) {
                $daily_purchases += $purchase;
            }
            // then it's in Monthly purchases (We need to differentiate between months that have different days count)

            if (date('Y-m-d', strtotime('-30 days')) >= $created_at) {
                $monthly_purchases += $purchase;
            }
        }

        // Sales
        foreach (Sale::whereIn('from_id', $this->getSubUsersForAuthenticatedUser())
            ->select('total', 'created_at')->get() as $sale) {
            $sale_info = $sale->total;
            $total_sales += $sale->total;
            $created_at = date('Y-m-d', strtotime($sale->created_at));
            if (date('Y-m-d') == $created_at) {
                $daily_sales += $sale_info;
            }
            if (date('Y-m-d', strtotime('-30 days')) >= $created_at) {
                $monthly_sales += $sale_info;
            }
        }

        $home_info = [
            'daily_purchases' => $this->setPercisionForFloatString($daily_purchases, 2, '.', ','),
            'daily_sales' => $this->setPercisionForFloatString($daily_sales, 2, '.', ','),
            'daily_profits' => $this->setPercisionForFloatString($daily_sales - $daily_purchases, 2, '.', ','),
        ];

        // Check If the User Is A Pharmacy Sub User
        if (Role::where('name', 'pharmacy_sub_user')->value('id') != $this->getAuthenticatedUserInformation()->role_id) {
            $home_info['total_purchases'] = $this->setPercisionForFloatString($total_purchases , 2 , '.' , ',');
            $home_info['total_sales'] = $this->setPercisionForFloatString($total_sales , 2 , '.' , ',');
            $home_info['total_profits'] = $this->setPercisionForFloatString($total_purchases - $total_sales , 2 , '.' , ',');
            $home_info['monthly_purchases'] = $this->setPercisionForFloatString($monthly_purchases , 2 , '.' , ',');
            $home_info['monthly_sales'] = $this->setPercisionForFloatString($monthly_sales , 2 , '.' , ',');
            $home_info['monthly_profits'] = $this->setPercisionForFloatString($monthly_purchases - $monthly_sales , 2 , '.' , ',');
        }
        $home_info['products'] = new productCollection(Product::whereIn('user_id', $this->getSubUsersForAuthenticatedUser())->limit(7)->get());

        return $this->resourceResponse($home_info);
    }
}
