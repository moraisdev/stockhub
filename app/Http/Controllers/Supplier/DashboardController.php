<?php

namespace App\Http\Controllers\Supplier;

//use App\Services\OrdersService;
use App\Services\SafeToPayService;
use Illuminate\Http\Request;

use App\Services\SupplierOrdersService;

use Auth;

//testes
use App\Models\SupplierOrders;
use App\Models\SupplierOrderShippings;
use App\Services\ChinaDivisionService;
use App\Models\SupplierOrderGroup;
use App\Services\Shop\OrdersService;
use App\Services\MelhorEnvioService;
use App\Models\FreteMelhorEnvio;

// atualizar imagens produtos que foram expirada pelo bling 
use App\Models\ProductImages;
use App\Services\BlingService;
use App\Models\Products;
use App\Services\Shop\CsvService;
use App\Models\ProductVariants;




class DashboardController extends Controller
{
    public function index(Request $request){
    	$supplier = Auth::user();

    	$service = new SupplierOrdersService($supplier);
    	$orders = $service->getPendingOrders(10);

    	if($request->input('teste') == 1){
            $service = new SafeToPayService();
            $service_response = $service->registerSupplierSubAccount($supplier);

            return redirect('/supplier')->with([$service_response['status'] => $service_response['message']]);
        }

        $dash_data = $service->getDashboardData();    

    	return view('supplier.dashboard.index', compact('orders', 'dash_data'));
    }
}
