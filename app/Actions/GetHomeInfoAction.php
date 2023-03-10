<?php
namespace App\Actions;

use App\Http\Resources\Api\V1\Product\ProductCollection;
use App\Models\V1\Product;
use App\Models\V1\Sale;
use App\Traits\RoleTrait;
use App\Traits\StringTrait;
use App\Traits\UserTrait;

class GetHomeInfoAction{

    use UserTrait, StringTrait, RoleTrait;

    /**
     * Get Home Page Statistics To Show
     * @return array
     */
    public function getInfo(): array
    {
        $totalPurchases = $totalSales =
        $dailyPurchases = $dailySales =
        $monthlyPurchases = $monthlySales = 0;

        $subUsers = $this->getSubUsersForUser();
        // Purchases
        foreach (Product::whereIn('user_id', $subUsers)
                     ->get(['original_total as total', 'created_at']) as $product) {
            // product info
            $purchase = $product->total;
            $created_at = date('Y-m-d', strtotime($product->created_at));

            $totalPurchases += $purchase;
            // Find Daily Purchases
            if (date('Y-m-d') == $created_at) {
                $dailyPurchases += $purchase;
            }
            // then it's in Monthly purchases (We need to differentiate between months that have different days count)

            if ($created_at >= date('Y-m-d', strtotime('- 29 days'))) {
                $monthlyPurchases += $purchase;
            }
        }

        // Sales
        foreach (Sale::whereIn('from_id', $subUsers)
                     ->select('total', 'created_at')->get() as $sale) {
            $sale_info = $sale->total;
            $totalSales += $sale->total;
            $created_at = date('Y-m-d', strtotime($sale->created_at));
            if (date('Y-m-d') == $created_at) {
                $dailySales += $sale_info;
            }
            if ($created_at >= date('Y-m-d', strtotime('-30 days'))) {
                $monthlySales += $sale_info;
            }
        }

        $dailyProfits = max($dailySales - $dailyPurchases , 0);
        $monthlyProfits = max($monthlySales - $monthlyPurchases,0);
        $totalProfits = max($totalSales - $totalPurchases,0);
        $homeInfo = [
            'daily_purchases' => $this->setPercisionForFloatString($dailyPurchases, 2, '.', ','),
            'daily_sales' => $this->setPercisionForFloatString($dailySales, 2, '.', ','),
            'daily_profits' => $this->setPercisionForFloatString($dailyProfits, 2, '.', ','),
        ];

        // Check If the User Is A Pharmacy Sub User
        if ($this->getRoleIdByName('pharmacy_sub_user') != $this->getAuthenticatedUserInformation()->role_id) {
            $homeInfo['total_purchases'] = $this->setPercisionForFloatString($totalPurchases, 2, '.', ',');
            $homeInfo['total_sales'] = $this->setPercisionForFloatString($totalSales, 2, '.', ',');
            $homeInfo['total_profits'] = $this->setPercisionForFloatString($totalProfits, 2, '.', ',');
            $homeInfo['monthly_purchases'] = $this->setPercisionForFloatString($monthlyPurchases, 2, '.', ',');
            $homeInfo['monthly_sales'] = $this->setPercisionForFloatString($monthlySales, 2, '.', ',');
            $homeInfo['monthly_profits'] = $this->setPercisionForFloatString($monthlyProfits, 2, '.', ',');
        }
        $homeInfo['products'] =
            new ProductCollection(
                Product::whereIn(
                    'products.user_id',
                    $subUsers
                )
                    ->with('product_details')
                    ->withSum('product_details' , 'qty')
                    ->limit(7)->get()
            );
        return $homeInfo;
    }
}
