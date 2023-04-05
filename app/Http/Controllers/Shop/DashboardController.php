<?php

namespace App\Http\Controllers\Shop;

use App\Services\Shop\OrdersService;
use Illuminate\Http\Request;
use App\Services\SafeToPayService;

use Auth;

use App\Models\SupplierOrderGroup;

use App\Services\MelhorEnvioService;
use App\Services\Shop\ShopifyService;
use App\Models\ShopContractedPlans;



class DashboardController extends Controller
{
    public function index(){
        $shop = Auth::guard('shop')->user();
 /*
        $plano = ShopContractedPlans::where('shop_id', $shop->id )->first();
        if($plano->subscription_status == 'inactive'){
            $shop->status = 'inactive';
            $shop->save();

        }
 */
        $ordersService = new OrdersService($shop);
        $orders = $ordersService->getPendingOrders(10);

        $dashboard_data = $ordersService->dashboardData();

    	return view('shop.dashboard.index', compact('orders', 'shop', 'dashboard_data'));
    }

    public function redirectMelhorEnvio(){
        return redirect('https://melhorenvio.com.br/oauth/authorize?client_id=1235&redirect_uri=https://mawa-melhor-envio.herokuapp.com/shop/settings&response_type=code&scope=cart-read cart-write companies-read companies-write coupons-read coupons-write notifications-read orders-read products-read products-write purchases-read shipping-calculate shipping-cancel shipping-checkout shipping-companies shipping-generate shipping-preview shipping-print shipping-share shipping-tracking ecommerce-shipping transactions-read users-read users-write');
    }
}
