<?php

namespace App\Http\Controllers\Supplier;

use App\Models\ReceiptOrders;
use App\Models\Receipts;
use App\Services\ExportService;
use App\Services\TotalExpressService;
use Illuminate\Http\Request;

use App\Services\SupplierOrdersService;
use App\Services\Shop\ShopifyService;
use App\Services\Shop\WoocommerceService;
use App\Services\Shop\CartxService;
use App\Services\Shop\YampiService;

use App\Models\SupplierOrders;
use App\Models\SupplierOrderShippings;

use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Services\BlingService;

use App\Services\ChinaDivisionService;
use App\Services\CurrencyService;
use App\Services\MelhorEnvioService;
use App\Models\ErrorLogs;
use App\Models\FreteMelhorEnvio;

use App\Mail\UpdatedTrackingCode;
use Mail;

use App\Models\OrderReturned;

use App\Models\Orders;
use App\Models\Customers;
use App\Models\CustomerAddresses;
use App\Models\ShippingLabel;
use App\Models\Shops;
use App\Services\Shop\MercadolivreService;
use App\Models\Mercadolivreapi;



class OrdersController extends Controller
{
    public function index(Request $request){
    	$supplier = Auth::user();

        $shipping_status = $request->status;
        
        $service = new SupplierOrdersService($supplier);

        switch ($shipping_status) {
            case 'pending':
    	       $orders = $service->getPendingOrders();

    	       return view('supplier.orders.index', compact('orders', 'shipping_status'));
            break;

            case 'sent':
               $orders = $service->getSentOrders();

               return view('supplier.orders.sent', compact('orders', 'shipping_status'));
            break;

            case 'completed':
               $orders = $service->getCompletedOrders();

               return view('supplier.orders.completed', compact('orders', 'shipping_status'));
            break;

            case 'returned':
                $orders = $service->getReturnedOrders();
 
                return view('supplier.orders.returned', compact('orders', 'shipping_status'));
             break;

            default:
               return redirect()->route('supplier.orders.index', ['status' => 'pending']);
            break;
        }
    }

    public function searchSent(Request $request){
        //verifica se o query e o filter estão setados, caso contrario redireciona para a index normal
        if($request->query('query') && $request->query('query') != '' && $request->query('filter') && $request->query('filter') != ''){
            set_time_limit(0);
            $supplier = Auth::user();

            $query = $request->query('query');
            $filter = $request->query('filter');

            $service = new SupplierOrdersService($supplier);

            $orders = $service->getSentOrdersSearch($query, $filter);

            $countOrdersSearch = $orders->total();

            $shipping_status = 'sent';

            return view('supplier.orders.sent', compact('orders', 'countOrdersSearch', 'query', 'filter', 'shipping_status'));
        }else{
            return redirect()->route('supplier.orders.index', ['status' => 'sent']);
        }        
    }

    public function chooseOrdersToSpreadsheet(Request $request){
        $supplier = Auth::user();
        $status = $request->status ? $request->status : 'pending';

        $orders = SupplierOrders::with('items')->with('order')
            ->where('supplier_id', $supplier->id)
            ->where('status', 'paid')
            ->orderBy('id', 'desc')
            ->whereHas('shipping', function($q) use ($status){
                $q->where('status', $status);
            });

        if($request->shop_id && $request->shop_id != 'all'){
            $shop_id = $request->shop_id;

            $orders->whereHas('order', function($q) use ($shop_id){
                $q->where('shop_id', $shop_id);
            });
        }

        if(($request->start_date && !$request->end_date) || ($request->end_date && !$request->start_date)){
            return redirect()->back()->with('error', 'Você deve selecionar ambas datas inicial e final para aplicar o filtro de período de tempo.');
        }

        if($request->start_date && $request->end_date){
            $orders->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->start_date)))->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->end_date)));
        }

        $orders = $orders->get();

        return view('supplier.orders.choose_orders_to_spreadsheet', compact('orders'));
    }

    public function generateSpreadsheet(Request $request){
        if($request->orders && count($request->orders) > 0){
            $orders_ids = array_keys($request->orders);
            $service = new ExportService();
            $file = $service->ordersToExcel($orders_ids);

            if($file != false){
                return $file;
            }else{
                return redirect()->back()->with('error', 'Selecione ao menos uma ordem para imprimir');
            }
        }else{
            return redirect()->back()->with('error', 'Selecione ao menos uma ordem para imprimir');
        }
    }

    public function show($order_id){
    	$supplier = Auth::user();

        $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);
        
        $dolar_price = CurrencyService::getDollarPrice();

    	return view('supplier.orders.show', compact('supplier_order', 'dolar_price'));
    }

    public function sendOrderToTotalExpress($order_id, Request $request){
        $supplier = Auth::user();
        $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);

        if($request->order_receipt){
            $receipt = new Receipts();

            $name = Str::random(15).$supplier->id . '.' . $request->order_receipt->extension();
            $path = $request->order_receipt->storeAs('receipts', $name, 's3');

            $file_name = env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/').$path;

            $receipt->supplier_id = $supplier->id;
            $receipt->shop_id = $supplier_order->order->shop_id;
            $receipt->type = 'order';
            $receipt->to = 'shop';
            $receipt->file = $file_name;
            $receipt->total_amount = $supplier_order->total_amount;

            $exportedToTotal = TotalExpressService::exportOrder($supplier_order, $request->order_receipt);

            if($exportedToTotal){
                $supplier_order->exported_to_total_express = 1;
                $supplier_order->save();

                if($receipt->save()){
                    $receipt_order = new ReceiptOrders();

                    $receipt_order->receipt_id = $receipt->id;
                    $receipt_order->supplier_order_id = $supplier_order->id;
                    $receipt_order->order_id = $supplier_order->order_id;

                    $receipt_order->save();
                }
            }else{
                return ['status' => 'error', 'message' => 'Não foi possível exportar esta remessa para a TotalExpress.'];
            }
        }else{
            $exportedToTotal = TotalExpressService::exportOrder($supplier_order);

            if($exportedToTotal){
                $supplier_order->exported_to_total_express = 1;
                $supplier_order->save();
            }else{
                return ['status' => 'error', 'message' => 'Não foi possível exportar esta remessa para a TotalExpress.'];
            }
        }

        return ['status' => 'success', 'message' => 'Pedido enviado à Total Express com sucesso.'];
    }

    public function uploadReceipt($order_id, Request $request){
        $supplier = Auth::user();
        $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);

        if($request->hasFile('order_receipt')){
            $receipt = new Receipts();

            $name = Str::random(15).$supplier->id . '.' . $request->order_receipt->extension();
            $path = $request->order_receipt->storeAs('receipts', $name, 's3');

            $file_name = env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/').$path;

            $receipt->supplier_id = $supplier->id;
            $receipt->shop_id = $supplier_order->order->shop_id;
            $receipt->type = 'order';
            $receipt->to = 'shop';
            $receipt->file = $file_name;
            $receipt->total_amount = $supplier_order->total_amount;

            if($receipt->save()){
                $receipt_order = new ReceiptOrders();

                $receipt_order->receipt_id = $receipt->id;
                $receipt_order->supplier_order_id = $supplier_order->id;
                $receipt_order->order_id = $supplier_order->order_id;

                $receipt_order->save();
            }

            if($supplier->shipping_method == 'total_express'){
                $exportedToTotal = TotalExpressService::exportOrder($supplier_order, $request->order_receipt);  

                if(!$exportedToTotal){
                    return back()->with('error', 'Não foi possível exportar esta remessa para a TotalExpress.');
                }
            }
        }

        if($request->hasFile('shipping_receipt')){
            $receipt = new Receipts();

            $name = Str::random(15).$supplier->id . '.' . $request->shipping_receipt->extension();
            $path = $request->shipping_receipt->storeAs('receipts', $name, 's3');

            $file_name = env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/').$path;

            $receipt->supplier_id = $supplier->id;
            $receipt->shop_id = $supplier_order->order->shop_id;
            $receipt->type = 'shipping';
            $receipt->to = 'shop';
            $receipt->file = $file_name;
            $receipt->total_amount = $supplier_order->order->amount;

            if($receipt->save()){
                $receipt_order = new ReceiptOrders();

                $receipt_order->receipt_id = $receipt->id;
                $receipt_order->supplier_order_id = $supplier_order->id;
                $receipt_order->order_id = $supplier_order->order_id;

                $receipt_order->save();
            }
        }

        return redirect()->back()->with('success', 'Notas fiscais atualizadas com sucesso.');
    }

    public static function updateAutomaticShippingMelhorEnvio($order_id, $supplier){
        try {
            $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);

            $shipping = SupplierOrderShippings::firstOrCreate(['supplier_id' => $supplier->id, 'supplier_order_id' => $supplier_order->id]);

            // Caso o pedido esteja sendo marcado como enviado
            if($shipping->status == null){
                foreach ($supplier_order->items as $item) {
                    if($item->variant->stock){
                        $item->variant->stock->quantity -= 1;

                        $item->variant->stock->save();
                    }
                }
            }

            $shipping->external_service = $supplier_order->order->external_service;

            if($supplier_order->order->external_service == 'shopify' /*&& $request->status == 'sent'*/ && $shipping->external_fulfillment_id == null){
                $response = ShopifyService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);
                
            }

            if($supplier_order->order->external_service == 'shopify' /*&& $request->status != 'pending'*/ && $shipping->external_fulfillment_id != null){
                $response = ShopifyService::updateFulfillment($supplier_order, $shipping);
                
            }
            if($supplier_order->order->external_service == 'woocommerce' /*&& $request->status == 'sent'*/ && $shipping->external_fulfillment_id == null){
                $response = WoocommerceService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);
                
            }

            if($supplier_order->order->external_service == 'woocommerce' /*&& $request->status != 'pending'*/ && $shipping->external_fulfillment_id != null){
                $response = WoocommerceService::updateFulfillment($supplier_order, $shipping);
                
            }

            //Cartx
            if($supplier_order->order->external_service == 'cartx' /*&& $request->status == 'sent'*/ && $shipping->external_fulfillment_id == null){
                $response = CartxService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);

                // if(!$response){
                //     return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                // }
            }

            if($supplier_order->order->external_service == 'cartx' /*&& $request->status != 'pending'*/ && $shipping->external_fulfillment_id != null){
                $response = CartxService::updateFulfillment($supplier_order, $shipping);

            }


            if($shipping->save()){

            }else{

                
            }
        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

        }
        
    }

    public function updateShipping($order_id, Request $request){
        
           
        
        try {
            
            $supplier = Auth::user();
            $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);

           

            $shipping = SupplierOrderShippings::firstOrCreate(['supplier_id' => $supplier->id, 'supplier_order_id' => $supplier_order->id]);

            if($shipping->status == null){
                foreach ($supplier_order->items as $item) {
                    if($item->variant->stock){
                        $item->variant->stock->quantity -= 1;

                        $item->variant->stock->save();
                    }
                }
            }

            if($request->status){
                $shipping->status = $request->status;
            }

            if($request->company){
                $shipping->company = $request->company;
            }

            if($request->tracking_url){
                $shipping->tracking_url = $request->tracking_url;
            }

            if($request->tracking_number){
                $shipping->tracking_number = $request->tracking_number;
            }

            $shipping->external_service = $supplier_order->order->external_service;

            if($supplier_order->order->external_service == 'shopify' && $request->status == 'sent' && $shipping->external_fulfillment_id == null){
                $response = ShopifyService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);

                if(!$response){
                    return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                }
            }

            if($supplier_order->order->external_service == 'shopify' && $request->status != 'pending' && $shipping->external_fulfillment_id != null){
                $response = ShopifyService::updateFulfillment($supplier_order, $shipping);

                if(!$response){
                    return redirect()->route('supplier.orders.index', ['status' => 'sent'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                }
            }

            if($supplier_order->order->external_service == 'shopify' && $request->status == 'pending' && $shipping->external_fulfillment_id != null){
                $response = ShopifyService::cancelFulfillment($supplier_order, $shipping);

                if(!$response){
                    return redirect()->route('supplier.orders.index', ['status' => 'sent'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                }

                $shipping->external_fulfillment_id = null;
            }

            //Cartx
            if($supplier_order->order->external_service == 'cartx' && $request->status == 'sent' && $shipping->external_fulfillment_id == null){
                $response = CartxService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);

                if(!$response){
                    return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                }
            }

            if($supplier_order->order->external_service == 'cartx' && $request->status != 'pending' && $shipping->external_fulfillment_id != null){
                $response = CartxService::updateFulfillment($supplier_order, $shipping);

                if(!$response){
                    return redirect()->route('supplier.orders.index', ['status' => 'sent'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                }
            }

            if($supplier_order->order->external_service == 'cartx' && $request->status == 'pending' && $shipping->external_fulfillment_id != null){
                $response = CartxService::cancelFulfillment($supplier_order, $shipping);

                if(!$response){
                    return redirect()->route('supplier.orders.index', ['status' => 'sent'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                }

                $shipping->external_fulfillment_id = null;
            }
            
            //woocommerce
            if($supplier_order->order->external_service == 'woocommerce' && $request->status == 'sent' && $shipping->external_fulfillment_id == null){
                $response = WoocommerceService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);

                if(!$response){
                    return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                }
            }

            if($supplier_order->order->external_service == 'woocommerce' && $request->status != 'pending' && $shipping->external_fulfillment_id != null){
                $response = WoocommerceService::updateFulfillment($supplier_order, $shipping);

                if(!$response){
                    return redirect()->route('supplier.orders.index', ['status' => 'sent'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                }
            }

            if($supplier_order->order->external_service == 'woocommerce' && $request->status == 'pending' && $shipping->external_fulfillment_id != null){
                $response = WoocommerceService::cancelFulfillment($supplier_order, $shipping);

                if(!$response){
                    return redirect()->route('supplier.orders.index', ['status' => 'sent'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                }

                $shipping->external_fulfillment_id = null;
            }

            //Yampi

            if($supplier_order->order->external_service == 'yampi' && $request->status == 'sent' && $shipping->external_fulfillment_id == null){

                $response = YampiService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);


                if(!$response){

                    return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');

                }

            }


            if($supplier_order->order->external_service == 'yampi' && $request->status != 'pending' && $shipping->external_fulfillment_id != null){

                $response = YampiService::updateFulfillment($supplier_order, $shipping);


                if(!$response){

                    return redirect()->route('supplier.orders.index', ['status' => 'sent'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');

                }

            }


            if($supplier_order->order->external_service == 'yampi' && $request->status == 'pending' && $shipping->external_fulfillment_id != null){

                $response = YampiService::cancelFulfillment($supplier_order, $shipping);


                if(!$response){

                    return redirect()->route('supplier.orders.index', ['status' => 'sent'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');

                }


                $shipping->external_fulfillment_id = null;

            }

            if($shipping->save()){

            if($supplier_order->exported_to_bling == 1){
                $supplier_order = $supplier_order->display_id;

                $bling_service = new BlingService();

               $loadedCodes = $bling_service->atualizarpedido($supplier_order , $supplier);
            }
                
                
                return redirect()->route('supplier.orders.index', ['status' => $request->status])->with('success', 'Código de rastreio do pedido atualizado com sucesso.');
            }else{
                return redirect()->route('supplier.orders.index', ['status' => $request->status])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
            }
        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

            return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
        }
        
    }

    public function updateShippingE($order_id, Request $request){
       
        $shipping = SupplierOrderShippings::where('supplier_order_id' , $order_id)->first();
        $supplier = Auth::user();
        $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);
        $order = Orders::where('id' , $supplier_order->order_id)->first();
        $apimercadolivre = Mercadolivreapi::where('shop_id' , $order->shop_id)->first();
        $shipping->external_service = $supplier_order->order->external_service;

        
        if($shipping->status == 'pending'){
            $shipping->status = 'sent';
            $shipping->external_service = $supplier_order->order->external_service;

            if($supplier_order->order->external_service == 'shopify' && $shipping->external_fulfillment_id == null){
                $response = ShopifyService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);
                if(!$response){
                    array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar no Shopify.');
                }
            }

            //Cartx
            if($supplier_order->order->external_service == 'cartx' && $shipping->external_fulfillment_id == null){
                $response = CartxService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);

                if(!$response){
                    array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar no Cartx.');
                }
            }

            //Woocommerce
            if($supplier_order->order->external_service == 'woocommerce' && $shipping->external_fulfillment_id == null){
                $response = WoocommerceService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);

                if(!$response){
                    array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar no Cartx.');
                }
            }

            //Yampi

            if($supplier_order->order->external_service == 'yampi' && $shipping->external_fulfillment_id == null){

                $response = YampiService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);

                if(!$response){

                    //return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');

                    array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar no Yampi.');

                }

            }

            if($supplier_order->exported_to_bling == 1){
                $supplier_order = $supplier_order->display_id;
    
                $bling_service = new BlingService();
    
               $loadedCodes = $bling_service->atualizarpedido($supplier_order , $supplier);
               return redirect()->route('supplier.orders.index')->with('success', 'O pedido selecionado foi atualizado para enviado com sucesso..');
             
    
            }

            
             //mercadolivre
            if($supplier_order->order->external_service == 'mercadolivre' && $shipping->external_fulfillment_id == null){
                
                $response = MercadolivreService::getAnuncio($apimercadolivre , $order);
                
                if ($response['status']== 200 ){

                    $shipping->tracking_number = $response['anuncio']->tracking_number;
                    $shipping->external_fulfillment_id = $response['anuncio']->id;

                }else {
                    $shipping->status =  $shipping->status = 'sent';
                    $shipping->save();
                    return redirect()->back()->with('error', 'Aconteceu algum erro inesperado. Não consiguimos buscar o rastreio no mercadolivre. alteramos para enviado');
                    
                }
                           
              

                if(!$response){
                    //return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                    array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar no Cartx.');
                }
            }

        

            if($shipping->save()){
                return redirect()->back()->with('success', ' Pedido atualizado com sucesso para enviado.');
            }else{
             //   array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar ordem para enviada.');
             return redirect()->back()->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
            }
        }else{
           
            return redirect()->route('supplier.orders.index')->with('erro','Sem valor de rastreio.');
        }
    }


    public function updateShippingSelected(Request $request){
        $supplier = Auth::user();

        if($request->arrSelectedOrdersSent && $request->arrSelectedOrdersSent != ''){

            $arrSelectedOrdersSent = explode(',', $request->arrSelectedOrdersSent);
        }

        $errorOrders = array();
        if(isset($arrSelectedOrdersSent) && count($arrSelectedOrdersSent) > 0){
            foreach ($arrSelectedOrdersSent as $order_id) {
                $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);
    
                $shipping = SupplierOrderShippings::firstOrCreate(['supplier_id' => $supplier->id, 'supplier_order_id' => $supplier_order->id]);
    
                // Caso o pedido esteja sendo marcado como enviado
                if($shipping->status == null){
                    foreach ($supplier_order->items as $item) {
                        if($item->variant->stock){
                            $item->variant->stock->quantity -= 1;
                            $item->variant->stock->save();
                        }
                    }
                }
    
                //só atualiza caso tenha pelo menos o valor do rastreio
                if($shipping->tracking_number && $shipping->tracking_number != NULL){
                    $shipping->status = 'sent';
                    $shipping->external_service = $supplier_order->order->external_service;
    
                    if($supplier_order->order->external_service == 'shopify' && $shipping->external_fulfillment_id == null){
                        $response = ShopifyService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);
                        if(!$response){
                            //return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                            array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar no Shopify.');
                        }
                    }
    
                    //Cartx
                    if($supplier_order->order->external_service == 'cartx' && $shipping->external_fulfillment_id == null){
                        $response = CartxService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);
    
                        if(!$response){
                            //return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                            array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar no Cartx.');
                        }
                    }
    
                    //Woocommerce
                    if($supplier_order->order->external_service == 'woocommerce' && $shipping->external_fulfillment_id == null){
                        $response = WoocommerceService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);
    
                        if(!$response){
                            //return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                            array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar no Cartx.');
                        }
                    }

                    //Yampi

                    if($supplier_order->order->external_service == 'yampi' && $shipping->external_fulfillment_id == null){

                        $response = YampiService::updateOrderShipping($supplier_order, $supplier_order->order->external_id, $shipping);

                        if(!$response){

                            //return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');

                            array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar no Yampi.');

                        }

                    }
    
                    if($shipping->save()){
                        //return redirect()->route('supplier.orders.index', ['status' => $request->status])->with('success', 'Código de rastreio do pedido atualizado com sucesso.');
                    }else{
                        array_push($errorOrders, $supplier_order->f_display_id.' - Erro ao atualizar ordem para enviada.');
                        //return redirect()->route('supplier.orders.index', ['status' => $request->status])->with('error', 'Aconteceu algum erro inesperado. Tente novamente em alguns minutos.');
                    }
                }else{
                    array_push($errorOrders, $supplier_order->f_display_id.' - Sem valor de rastreio.');
                }
            }
        }
        

        if(count($errorOrders) > 0){
            return redirect()->route('supplier.orders.index', ['status' => 'pending', 'errorOrders' => $errorOrders])->with('info', 'Algumas ordens não puderam ser atualizadas.'.implode(" ", $errorOrders));
        }else{
            return redirect()->route('supplier.orders.index', ['status' => 'pending'])->with('success', 'As ordens selecionadas foram atualizadas para enviado com sucesso.');
        }
    }

    public function printTag($order_id){
        $supplier = Auth::user();
        $mytime = date('Y-m-d H:i:s');
       

        

        if(!$supplier->address){
            return redirect()->route('supplier.orders.index')->with('error', 'Você deve atualizar o endereço em seu perfil antes de criar etiquetas de frete.');
        }

        $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);
         $shipping = SupplierOrderShippings::where('supplier_id', $supplier->id)->where('supplier_order_id' , $order_id )->first();
        
      
         if ($shipping->external_service == 'bling_service') {

            return view('supplier.orders.tag_bling', compact('supplier_order',  'shipping'  ));    
        }

        if ($shipping->external_service == 'mercadolivre') {
            $order = Orders::where('id' , $supplier_order->order_id)->first();
            $apimercadolivre = Mercadolivreapi::where('shop_id' , $order->shop_id)->first();	   
         
            $gerorder =  MercadolivreService::getAnuncio($apimercadolivre , $order );
           
            
           
            if ($order->shipping_ml){

                if ($gerorder['status'] == 401){
                    $tokenml = MercadolivreService::getToken($order, $apimercadolivre );
                    $token = Mercadolivreapi::where('shop_id',$apimercadolivre->shop_id)->first();
                    $token->token = $tokenml;
                    $token->token_exp = date($mytime, strtotime('+4 Hours'));
                    $token->save();

                }   

             if ($gerorder['status'] == 200){                 
                $order->tracking_number = $gerorder['anuncio']->tracking_number;
                $order->tracking_servico = $gerorder['anuncio']->tracking_method;
                $order->save();
                if($supplier->imp_etq_ml == 0){
                $dowalods =  MercadolivreService::imprimirEtiqueta($apimercadolivre , $order );
               
                           
                }elseif ($supplier->imp_etq_ml == 1){
                  $dowalods =  MercadolivreService::imprimirEtiquetatermica($apimercadolivre , $order );
                 
                }


             }    
                
            }elseif ($gerorder['status'] == 401) {
                return redirect()->back()->with('error', 'Arquivo não existe para baixar a etiqueta no mercadolivre.');
            }
        } else {

            return redirect()->back()->with('error', 'Arquivo não existe para baixar a etiqueta no mercadolivre.');				

        }

        if ($shipping->external_service == 'planilha') {

            
           
            $order = Orders::where('id' , $supplier_order->order_id)->first();
            $dowalods = ShippingLabel::where('id' , $order->shipping_label_id)->first();                   
        
           
            
            if (!$dowalods->url_labels) {

               
                return redirect()->back()->with('error', 'Arquivo não existe para baixar a etiqueta e.');
            
            }


            $file= public_path(). "/etiqueta/".$dowalods->url_labels;
            return response()->download($file);

        }    
        

    }

    public function printContentDeclaration($order_id){
        $supplier = Auth::user();

        if(!$supplier->address){
            return redirect()->back()->with('error', 'Você deve atualizar o endereço em seu perfil antes de criar etiquetas de frete.');
        }

        $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);

        if(!$supplier_order->order || !$supplier_order->order->customer || !$supplier_order->order->customer->address){
            return redirect()->back()->with('error', 'Não há nenhum cliente atribuído à este pedido e não é possível gerar a declaração de conteúdo.');
        }

        return view('supplier.orders.content_declaration', compact('supplier', 'supplier_order'));
    }

    public function jsonOrder($order_id){
        $supplier = Auth::user();


        return SupplierOrders::with('shipping', 'receipts')->where('supplier_id', $supplier->id)->find($order_id);
    }

    public function cancel($order_id){
        $supplier = Auth::user();

        $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);

        $supplier_order->order->status = 'canceled';
        $supplier_order->order->save();

        $supplier_order->status = 'canceled';
        $supplier_order->save();

        return redirect()->route('supplier.orders.index', ['pending'])->with('success', 'Pedido cancelado com sucesso. O lojista foi notificado do cancelamento.');
    }

    public function downloadReceipt($receipt_id){
        $supplier = Auth::user();
        $receipt = Receipts::where('supplier_id', $supplier->id)->find($receipt_id);

        if(!$receipt){
            return redirect()->back()->with('error', 'Você não tem permissão para efetuar o download desta nota fiscal.');
        }

        $file_name = str_replace(env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/'), '', $receipt->file);

        return Storage::disk('s3')->download($file_name);
    }

    public function printTagsMelhorEnvio(Request $request){
        $supplier = Auth::user();

        $arrIds = explode(',', $request->print_tags_melhor_envio);
        $supplierOrders = SupplierOrders::whereIn('id', $arrIds)
                                        ->where('supplier_id', $supplier->id)
                                        ->get();
        
        $melhorenvio = FreteMelhorEnvio::where('supplier_order_id', $supplier->id)->get();    
       
        $orders = SupplierOrders::whereIn('id', $arrIds)
                                        ->where('supplier_id', $supplier->id)
                                        ->first();
        $order = Orders::where('id', $orders->order_id)->first(); 
                                    

        //cria um array com os ids da melhor envio e envia todos em uma única requisição
        $arrIdsMelhorEnvio = [];
        $melhorEnvioService = new MelhorEnvioService();

               
        //dd($melhorenvio);
        
        foreach ($supplierOrders as $orders) {
           
           
            $fretemelhorenvio = false;
            
            $order = Orders::where('id', $orders->order_id)->first();  
            $customer = CustomerAddresses::where('customer_id', $order->customer_id)->first(); 
            $shop = Shops::where('id', $order->shop_id)->first(); 
            $customerd = Customers::where('id', $order->customer_id)->first(); 

            
            $melhorEnvioService = new MelhorEnvioService();

           
            if (FreteMelhorEnvio::where('order_id', $orders->order_id)->count() == 0) {
            $fretemelhorenvio = $melhorEnvioService->quoteBuyFreight($supplier, $shop, $customer, $order , $customerd );
            
           
            if ($fretemelhorenvio != false) {
            FreteMelhorEnvio::create([
                'order_id' => $orders->order_id,
                'supplier_id' => $orders->supplier_id,
                'supplier_order_id'=> $orders->id, 
                'service_id' => $fretemelhorenvio->serviceId,
                'melhor_envio_id' => $fretemelhorenvio->freteId,
                'amount' => $fretemelhorenvio->valor,
                'protocol' => $fretemelhorenvio->protocol,
                'status' => $fretemelhorenvio->status,
                
            ]);
        
            $idmelhorenvio =  FreteMelhorEnvio::where('order_id', $orders->order_id)->first();
          
            $comprarfrete = $melhorEnvioService->payCartNew($idmelhorenvio->melhor_envio_id);    
           
          
            if($comprarfrete != false){
            FreteMelhorEnvio::where('order_id', $orders->order_id)->update(['tracking' => $comprarfrete->data[0]->tracking]);
            }else {
                return redirect()->back()->with('info', 'Erro Adquirir Frete Entre em Contato Suporte..');
                break;

            }
            
            $responseMelhorEnvio2 = $melhorEnvioService->printTag($idmelhorenvio->melhor_envio_id);
            
            if($responseMelhorEnvio2 != false){
            $public = '?public=true';
            FreteMelhorEnvio::where('order_id', $orders->order_id)->update(['tag_url' => $responseMelhorEnvio2->url . $public ]);
            }           
            $shipping_sup =  SupplierOrderShippings::where('supplier_order_id', $orders->id)->first();
            if ($shipping_sup->tracking_number == null) {
              SupplierOrderShippings::where('supplier_order_id', $orders->id)->update(['tracking_number' => $idmelhorenvio->tracking , 'company' => 'Melhor Envio' , 'tracking_url' => $idmelhorenvio->tracking ]);
             
            } 
        
        }        
        
        } else {

            $melhorEnvioService = new MelhorEnvioService();
            $idmelhorenvio =  FreteMelhorEnvio::where('order_id', $orders->order_id)->first();
          
             if ($idmelhorenvio->tag_url == null){

                $responseMelhorEnvio2 = $melhorEnvioService->printTag($idmelhorenvio->melhor_envio_id);
                $public = '?public=true';
                FreteMelhorEnvio::where('order_id', $orders->order_id)->update(['tag_url' => $responseMelhorEnvio2->url . $public ]);
             }

             if ($idmelhorenvio->tracking == null){

                $responseMel = $melhorEnvioService->consultProt($idmelhorenvio->melhor_envio_id);                 
                FreteMelhorEnvio::where('order_id', $orders->order_id)->update(['tracking' => $responseMel->data[0]->tracking]);
             }
          
             $shipping_sup =  SupplierOrderShippings::where('supplier_order_id', $orders->id)->first();
             if ($shipping_sup->tracking_number == null) {
               SupplierOrderShippings::where('supplier_order_id', $orders->id)->update(['tracking_number' => $idmelhorenvio->tracking , 'company' => 'Melhor Envio' , 'tracking_url' => $idmelhorenvio->tracking ]);
              
             } 
          
            
            }         
       
        
                   
          $count = FreteMelhorEnvio::where('order_id', $orders->order_id)->count();
          if (($fretemelhorenvio == false) and ($count == 0 )) {  
            return redirect()->back()->with('info', 'Erro ao Emitir etiqueta confirme os dados..');
            break;
          }        
        }

        if ($idmelhorenvio->tag_url != null){
            redirect()->to($idmelhorenvio->tag_url)->send();

        }

        if(count($arrIdsMelhorEnvio) > 0){
            $linkEtiquetas = $melhorEnvioService->getLinkTags($arrIdsMelhorEnvio);
            if($linkEtiquetas){
                return redirect($linkEtiquetas); //redireciona pro link das etiquetas
            }
        }

      return redirect()->back()->with('info', 'Erro ao Emitir etiqueta confirme os dados.');
    }

    public function printPendingTags(){
        $supplier = Auth::user();

        if(!$supplier->address){
            return redirect()->route('supplier.orders.index')->with('error', 'Você deve atualizar o endereço em seu perfil antes de criar etiquetas de frete.');
        }

        $supplier_orders = SupplierOrders::where('supplier_id', $supplier->id)
                                         ->whereHas('shipping', function($q){
                                             $q->where('status', 'pending');
                                         })
                                         ->where('status', 'paid')
                                         ->get();

        return view('supplier.orders.pending_tags', compact('supplier_orders'));
    }

    public function printPendingContentDeclaration(){
        $supplier = Auth::user();

        if(!$supplier->address){
            return redirect()->route('supplier.orders.index')->with('error', 'Você deve atualizar o endereço em seu perfil antes de criar etiquetas de frete.');
        }

        $supplier_orders = SupplierOrders::where('supplier_id', $supplier->id)
            ->whereHas('shipping', function($q){
                $q->where('status', 'pending');
            })
            ->where('status', 'paid')
            ->get();

        return view('supplier.orders.pending_content_declarations', compact('supplier_orders', 'supplier'));
    }

    public function updateComments($order_id, Request $request){
        $supplier = Auth::user();

        $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($order_id);

        $supplier_order->comments = $request->comments;
        $supplier_order->save();

        return redirect()->back()->with('success', 'Anotações atualizadas com sucesso.');
    }

    public function updateOrderTrackingNumberBling(Request $request){
        //recebe um vetor de orders id e verifica no bling se esse id tem o número de rastreio, se tiver, salva esse número
        if($request->arrOrdersId && $request->arrOrdersId != ''){

            $arrOrdersId = explode(',', $request->arrOrdersId);

            $bling_service = new BlingService();

            $loadedCodes = $bling_service->checkOrderTrackingNumber($arrOrdersId);
            if($loadedCodes && count($loadedCodes) > 0){
                return redirect()->back()->with('success', count($loadedCodes).' códigos de rastreio importados com sucesso.');
            }else{
                return redirect()->back()->with('info', 'Nenhum código de rastreio foi importado.');
            }
        }
    }

    public function updateOrderTrackingNumberChinaDivision(Request $request){
        //recebe um vetor de orders id e verifica no china division se esse id tem o número de rastreio, se tiver, salva esse número
        if($request->arrOrdersId && $request->arrOrdersId != ''){

            $arrOrdersId = explode(',', $request->arrOrdersId);

            $china_division_service = new ChinaDivisionService();

            $loadedCodes = $china_division_service->checkOrderTrackingNumber($arrOrdersId);

            if($loadedCodes && count($loadedCodes) > 0){
                return redirect()->back()->with('success', count($loadedCodes).' códigos de rastreio importados com sucesso.');
            }else{
                return redirect()->back()->with('info', 'Nenhum código de rastreio foi importado.');
            }
        }
    }

    public function updateManualMelhorEnvio(Request $request, $order_id){
        $s = SupplierOrders::find($order_id);

        if($s){ //verifica se é uma ordem válida
            //pega os dados do protocolo na melhor envio
            $melhorEnvioService = new MelhorEnvioService();

            $responseMelhorEnvio = $melhorEnvioService->consultProtocol($request->protocol);
            //salva o id vindo da melhor envio
            if($s->supplier->shipping_method == 'melhor_envio' && $responseMelhorEnvio && $responseMelhorEnvio->id != ''){
                $freteMelhorEnvio = FreteMelhorEnvio::firstOrCreate([
                    'order_id' => $s->order->id,
                    'supplier_id' => $s->supplier->id,
                    'supplier_order_id' => $s->id
                ]);
                
                $freteMelhorEnvio->amount = $responseMelhorEnvio->price; //valor do frete
                $freteMelhorEnvio->service_id = $responseMelhorEnvio->service_id; //id to tipo de serviço 1 - PAC, 2 - SEDEX, 3 - Mini Envios
                $freteMelhorEnvio->status = $responseMelhorEnvio->status;                    
                $freteMelhorEnvio->melhor_envio_id = $responseMelhorEnvio->id; //id do frete adicionado ao carrinho da melhor envio
                $freteMelhorEnvio->protocol = $responseMelhorEnvio->protocol; //salva o protocolo
                
                if($freteMelhorEnvio->save()){
                    return redirect()->back()->with('success','Frete adicionado manualmente com sucesso.');
                }
            }
        }

        return redirect()->back()->with('error', 'Erro ao cadastrar frete manualmente.');
    }

    public function destroy(Request $request){
        if($request->order_id /*&& Auth::guard('admin')->check()*/){
            $supplier = Auth::user();

            $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($request->order_id);
            if($supplier_order->delete()){
                return redirect()->back()->with('success', 'Pedido excluido com sucesso.');
            }            
        }
    }

    public function updateReturned(Request $request){
        //marca o pedido como returned e cria o objeto de order returned para salvar o status desse evento
        $supplier = Auth::user();
        $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($request->order_id);
        if($supplier_order){
            $supplier_order->status = 'returned';
            if($supplier_order->save()){
                $orderReturned = OrderReturned::create([
                    'order_id' => $supplier_order->order->id,
                    'supplier_order_id' => $supplier_order->id,
                    'shop_id' => $supplier_order->order->shop_id,
                    'supplier_id' => $supplier_order->supplier_id
                ]);

                if($orderReturned){
                    return redirect()->back()->with('success', 'Pedido marcado como devolvido com sucesso.');
                }
            }
        }
        return redirect()->back()->with('error', 'Erro ao devolver pedido.');
    }
}
