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
        
        

        //CÓDIGO DE TESTES
        // $group = SupplierOrderGroup::where('transaction_id', 26852273)->first();
        // if($group){
        //     OrdersService::paymentReceived($group);
        // }
        

        // $china_service = new ChinaDivisionService();
        // $orders = SupplierOrders::where('status', 'paid')
        //                         ->where('exported_to_china_division', 0)
        //                         ->where('supplier_id', 56)
        //                         ->orderBy('id', 'desc')->get();

        // foreach($orders as $o){
        //     $idOrder = $china_service->generateOrder($o);

        //     // if($idOrder){ //caso seja um código válido, salva
        //     //     $shipping = SupplierOrderShippings::where('supplier_id', $o->supplier_id)
        //     //                                     ->where('supplier_order_id', $o->id)
        //     //                                     ->first();
        //     //     if($shipping){
        //     //         //$shipping->status = 'sent';
        //     //         //$shipping->tracking_url = "https://www2.correios.com.br/sistemas/rastreamento/default.cfm/";
        //     //         //$shipping->tracking_number = $idOrder;
        //     //         //$shipping->save();

        //     //         $o->exported_to_china_division = 1;
        //     //         $o->save();

        //     //         //echo "código: ".$trackingNumberCorreios."<br>";
        //     //     }                    
        //     // }   
        // }

                                        
                                        

        

        //FIM CÓDIGO DE TESTES
        
        

    	return view('supplier.dashboard.index', compact('orders', 'dash_data'));
    }
}
