<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Services\Shop\CatalogService;
use App\Services\Shop\ProductsService;
use App\Services\Shop\OrdersService;

use Auth;

class CatalogController extends Controller
{
    public function index(){
    	$shop = Auth::guard('shop')->user();
        if($shop->status == 'inactive'){
            return redirect()->back()->with('error', 'O pagamento de sua assinatura está pendente e o acesso ao catálogo de produtos foi desativado.');
        }
        
        $ordersService = new OrdersService($shop);
        $dashboard_data = $ordersService->dashboardData();
    	$products = CatalogService::getPublicProducts($shop);
        $categories = CatalogService::getProductCategories($shop);
       

    	return view('shop.catalog.index', compact('products', 'dashboard_data', 'categories'));
    }
    
}
