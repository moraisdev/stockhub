<?php

namespace App\Http\Controllers\Shop;

use Auth;
use MPSdk;
use MPItem;
use MPPreference;
use App\Models\Orders;
use App\Models\Returns;
use App\Models\Receipts;
use App\Models\Customers;
use App\Models\CustomerAddresses;
use App\Models\Discounts;
use App\Models\Suppliers;
use App\Models\OrderItems;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\OrdersImport;
use App\Models\ReceiptOrders;
use App\Models\ReturnMessages;
use App\Models\SupplierOrders;
use App\Models\PaymentGateways;
use App\Models\ProductVariants;
use App\Services\CurrencyService;
use App\Services\PaymentsService;
use App\Services\Shop\CsvService;

use App\Imports\OrdersItemsImport;
use App\Models\SupplierOrderGroup;
use App\Models\CouponOrderReturned;

use App\Models\OrderGroupPayments;
use App\Models\SupplierOrderItems;
use App\Exceptions\CustomException;

use App\Services\Shop\CartxService;
use App\Services\Shop\YampiService;
use App\Services\MercadoPagoService;
use App\Services\Shop\OrdersService;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Shop\ShopifyService;
use App\Models\SupplierOrderShippings;
use App\Models\OrderShippings;
use App\Services\Shop\ProductsService;
use App\Models\SupplierOrdersDiscounts;

use App\Services\Gerencianetpay;

use App\Services\SupplierOrdersService;
use Illuminate\Support\Facades\Storage;
use App\Services\ProductVariantsService;
use App\Services\Shop\WoocommerceService;

use App\Services\Shop\BlingServiceShop;

use App\Services\CorreiosService;
use App\Services\TotalExpressService;
use App\Services\MelhorEnvioService;
use App\Services\ChinaShippingService;
use Illuminate\Support\Facades\Log;
use Safe2Pay\Models\Payment\Pix;
use App\Models\Admins;
use App\Services\Shop\MercadolivreService;
use App\Models\Mercadolivreapi;
use App\Models\Products;
use App\Models\ShopProducts;
use Dsc\MercadoLivre\Resources\Order\Order;

use App\Services\CitelService;

class OrdersController extends Controller
{
    public function index(){
        set_time_limit(0);
    	$shop = Auth::guard('shop')->user();
        if($shop->status == 'inactive'){
            return redirect()->back()->with('error', 'O pagamento de sua assinatura está pendente e o acesso ao pedidos foi desativado.');
        }
        
        $ordersService = new OrdersService($shop);
        $ordersService->clearNoCustomerOrders();
        $orders = $ordersService->getPendingOrders();
        $mercadolivre = self::importOrderMercadolivre2();
        $apimercadolivreapi = Mercadolivreapi::where('shop_id' , $shop->id )->first();
       
        //carrega o valor do dólar
//        $dolar_price = CurrencyService::getDollarPrice();

        //quantidade total de pedidos pendentes
        $countOrders = $ordersService->getTotalCountPendingOrders();
        return view('shop.orders.index', compact('orders', 'countOrders', 'apimercadolivreapi' ));
    }

    public function deleteGroup(Request $request){        
        $shop = Auth::guard('shop')->user();
        $ordersService = new OrdersService($shop);

        $deletedGroup = $ordersService->deletePendingOrderGroup($request->group_id);

        if($deletedGroup){
            return redirect()->back()->with('success', 'Fatura '.$request->group_id.' excluída com sucesso.');
        }
        return redirect()->back()->with('error', 'Erro ao excluir fatura '.$request->group_id.'.');
    }

    public function deleteOrderInGroup(Request $request){ 
        $shop = Auth::guard('shop')->user();
        $ordersService = new OrdersService($shop);

        $deletedOrderInGroup = $ordersService->deletePendingOrderInGroup($request->group_id, $request->order_id);

        if($deletedOrderInGroup){
            return redirect()->back()->with('success', 'O pedido foi excluído dessa fatura com sucesso.');
        }
        return redirect()->back()->with('error', 'Erro ao excluir o pedido dessa fatura.');
    }

    public function search(Request $request){
        //verifica se o query e o filter estão setados, caso contrario redireciona para a index normal
        if($request->query('query') && $request->query('query') != '' && $request->query('filter') && $request->query('filter') != ''){
            set_time_limit(0);
            $shop = Auth::guard('shop')->user();

            //carrega o valor do dólar
            $dolar_price = CurrencyService::getDollarPrice();

            $query = $request->query('query');
            $filter = $request->query('filter');

            $ordersService = new OrdersService($shop);
            $ordersService->clearNoCustomerOrders();

            $orders = $ordersService->getPendingOrdersSearch($query, $filter);

            $countOrdersSearch = $orders->total();

            $countOrders = $ordersService->getTotalCountPendingOrders();
            

            return view('shop.orders.index', compact('orders', 'dolar_price', 'countOrders', 'countOrdersSearch', 'query', 'filter'));
        }else{
            return redirect()->route('shop.orders.index');
        }        
    }

    public function sentSearch(Request $request){
        //verifica se o query e o filter estão setados, caso contrario redireciona para a index normal
        
        if($request->query('query') && $request->query('query') != '' && $request->query('filter') && $request->query('filter') != ''){
            set_time_limit(0);
            $shop = Auth::guard('shop')->user();

            //carrega o valor do dólar
            $dolar_price = CurrencyService::getDollarPrice();

            $query = $request->query('query');
            $filter = $request->query('filter');

            $ordersService = new OrdersService($shop);
            $ordersService->clearNoCustomerOrders();

            $orders = $ordersService->getSentOrdersSearch($query, $filter);

            $countOrdersSearch = $orders->total();            

            return view('shop.orders.sent', compact('orders', 'dolar_price', 'countOrdersSearch', 'query', 'filter'));
        }else{
            return redirect()->route('shop.orders.sent');
        }        
    }

    public function paid(){
        $shop = Auth::guard('shop')->user();

        $ordersService = new OrdersService($shop);
        $orders = $ordersService->getPaidOrders();

        return view('shop.orders.paid', compact('orders'));
    }

    public function sent(){
        $shop = Auth::guard('shop')->user();

        $ordersService = new OrdersService($shop);
        $orders = $ordersService->getSentOrders();

        return view('shop.orders.sent', compact('orders'));
    }

    public function completed(){
        $shop = Auth::guard('shop')->user();

        $ordersService = new OrdersService($shop);
        $orders = $ordersService->getDeliveredSupplierOrders();

        return view('shop.orders.completed', compact('orders'));
    }

    public function returned(){
        $shop = Auth::guard('shop')->user();

        $ordersService = new OrdersService($shop);
        $orders = $ordersService->getReturnedOrders();
        return view('shop.orders.returned', compact('orders'));
    }

    public function solveReturned(Request $request, $order_id){
        try {
            $shop = Auth::guard('shop')->user();

            $ordersService = new OrdersService($shop);
            $order = $ordersService->findReturnedOrder($order_id);

            if($order){ //caso seja uma ordem válida
                if($request->opt_order_returned == 1){ //caso seja opção de reembolso
                    //salva o cupom
                    $couponReturned = CouponOrderReturned::firstOrCreate([
                        'order_returned_id' => $order->id,
                        'amount' => $order->supplier_order->amount,
                        'supplier_id' => $order->supplier_order->supplier->id
                    ]);

                    if($couponReturned){
                        //atualiza a ordem como resolvida e o tipo de resolução
                        $order->decision = 'credit';
                        $order->status = 'solved';

                        if($order->save()){
                            return redirect()->back()->with('success', 'Devolução resolvida com sucesso.');
                        }
                    }
                }else{ //caso seja a opção de reenvio
                    //redireciona para a página para confirmar o endereço e gerar a nova fatura para o lojista pagar o novo frete
                    return redirect()->route('shop.orders.check_resend', ['order_id' => $order_id]);
                }
            }

            return redirect()->back()->with('error', 'Erro ao salvar resolução, contate a administração.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao salvar resolução, contate a administração.');
        }
    }

    public function checkResendOrder(Request $request, $order_id){
        try {
            $shop = Auth::guard('shop')->user();

            $ordersService = new OrdersService($shop);
            $orderReturned = $ordersService->findReturnedOrder($order_id);

            if($orderReturned){ //caso seja uma ordem válida
                //exibe os dados da ordem, mas só com o pedido e frete desse fornecedor que foi devolvido
                $order = $ordersService->find($orderReturned->order_id);

                $variantsService = new ProductVariantsService(null, $shop);
                $variants = $variantsService->get();

                //carrega o valor do dólar
                $dolar_price = CurrencyService::getDollarPrice();        

                return view('shop.orders.check_resend', compact('order', 'variants', 'dolar_price', 'orderReturned'));
            }

            return redirect()->back()->with('error', 'Erro ao salvar resolução, contate a administração.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao salvar resolução, contate a administração.');
        }
    }

    public function generateResendInvoice(Request $request){
        $idPedidoGerado = 0;
        try {
            $shop = Auth::guard('shop')->user();
            $order_id = $request->order_returned_id;
            $ordersService = new OrdersService($shop);
            $orderReturned = $ordersService->findReturnedOrder($order_id);

            if($orderReturned){ //caso seja uma ordem válida
                $external_id = 'r_'.$orderReturned->id.'_'.$orderReturned->order->external_id;
                //antes realiza uma cotação na melhor pra ver se é um local válido
                //adiciona o cliente novamente
                $customer = new Customers();
                $customer->shop_id = $orderReturned->order->shop_id;
                $customer->external_id = $external_id;
                $customer->external_service = $orderReturned->order->external_service;
                $customer->first_name = $orderReturned->order->customer->first_name;
                $customer->last_name = $orderReturned->order->customer->last_name;
                $customer->email = $orderReturned->order->customer->email;
                $customer->total_spent = $orderReturned->order->customer->total_spent;                
                $customer->save();

                $provice_codes = (object)[
                    (object)['code' => "AC", 'name' => 'Acre'],
                    (object)['code' => "AL", 'name' => 'Alagoas'],
                    (object)['code' => "AP", 'name' => 'Amapá'],
                    (object)['code' => "AM", 'name' => 'Amazonas'],
                    (object)['code' => "BA", 'name' => 'Bahia'],
                    (object)['code' => "CE", 'name' => 'Ceará'],
                    (object)['code' => "DF", 'name' => 'Distrito Federal'],
                    (object)['code' => "ES", 'name' => 'Espírito Santo'],
                    (object)['code' => "GO", 'name' => 'Goiás'],
                    (object)['code' => "MA", 'name' => 'Maranhão'],
                    (object)['code' => "MT", 'name' => 'Mato Grosso'],
                    (object)['code' => "MS", 'name' => 'Mato Grosso do Sul'],
                    (object)['code' => "MG", 'name' => 'Minas Gerais'],
                    (object)['code' => "PA", 'name' => 'Pará'],
                    (object)['code' => "PB", 'name' => 'Paraíba'],
                    (object)['code' => "PR", 'name' => 'Paraná'],
                    (object)['code' => "PE", 'name' => 'Pernambuco'],
                    (object)['code' => "PI", 'name' => 'Piauí'],
                    (object)['code' => "RJ", 'name' => 'Rio de Janeiro'],
                    (object)['code' => "RN", 'name' => 'Rio Grande do Norte'],
                    (object)['code' => "RS", 'name' => 'Rio Grande do Sul'],
                    (object)['code' => "RO", 'name' => 'Rondônia'],
                    (object)['code' => "RR", 'name' => 'Roraima'],
                    (object)['code' => "SC", 'name' => 'Santa Catarina'],
                    (object)['code' => "SP", 'name' => 'São Paulo'],
                    (object)['code' => "SE", 'name' => 'Sergipe'],
                    (object)['code' => "TO", 'name' => 'Tocantins']
                ];
                
                $provinceName = '';
                
                foreach($provice_codes as $state){ //isso aqui é preguiça/sono
                    if($state->code == $request->province_code){
                        $provinceName = $state->name;
                    }
                }

                //adiciona o novo endereço (ou antigo)
                $address = new CustomerAddresses();
                $address->customer_id = $customer->id;
                $address->address1 = $request->address1;
                $address->name = $orderReturned->order->customer->address->name;
                $address->company = $request->company;
                $address->address2 = $request->address2;
                $address->city = $request->city;
                $address->province = $provinceName;
                $address->country = 'Brazil';
                $address->zipcode = $request->zipcode;
                $address->phone = $request->phone;
                $address->province_code = $request->province_code;
                $address->country_code = 'BR';
                $address->save();

                $shipping_amount = self::calculateShipping($orderReturned->supplier_order->items, $orderReturned->supplier_order->supplier, $customer);
                
                //cria uma cópia da ordem só com os produtos da SupplierOrder que foi devolvida
                $newOrder = new Orders();
                $newOrder->shop_id = $orderReturned->order->shop_id;
                $newOrder->external_id = $external_id;
                $newOrder->external_service = $orderReturned->order->external_service;
                $newOrder->name = $orderReturned->order->name;
                $newOrder->email = $orderReturned->order->email;
                $newOrder->external_price = $orderReturned->order->external_price;
                $newOrder->external_usd_price = $orderReturned->order->external_usd_price;
                $newOrder->status = 'pending';


                $newOrder->save();

                $idPedidoGerado = $newOrder->id;

                //adiciona todos os itens da ordem
                // POR ENQUANTO SÓ TEM COMO DEVOLVER A ORDEM TODA
                $items = array();
                $items_amount = 0;
                foreach($orderReturned->supplier_order->items as $item){
                    $newItem = new OrderItems();
                    $newItem->order_id = $newOrder->id;
                    $newItem->product_variant_id = $item->product_variant_id;
                    $newItem->external_service = $item->external_service;
                    $newItem->external_product_id = $item->external_product_id;
                    $newItem->external_variant_id = $item->external_variant_id;
                    $newItem->sku = $item->sku;
                    $newItem->title = $item->title;
                    $newItem->quantity = $item->quantity;
                    $newItem->amount = $item->amount;
                    $newItem->external_price = $item->external_price;
                    $newItem->charge = $item->charge;
                    $newItem->save();
                    //$items_amount += $item->amount;
                    array_push($items, $newItem);
                }

                $newOrder->customer_id = $customer->id;
                $newOrder->items_amount = $items_amount;
                $newOrder->shipping_amount = $shipping_amount;
                $newOrder->amount = $items_amount + $shipping_amount;
                $newOrder->save();

                //salva os dados de envio
                $shipping = new OrderShippings();
                $shipping->supplier_id = $orderReturned->supplier_order->supplier->id;
                $shipping->order_id = $newOrder->id;
                $shipping->amount = $shipping_amount;
                $shipping->save();

                //criar um novo grupo de faturas SupplierOrderGroup com somente essa fatura
                $return = OrdersService::generate_supplier_orders_individual_order($newOrder);

                if($return && $return['total_in_supplier_orders']){
                    //atualiza o decision da OrderReturned para 'resend'
                    $orderReturned->decision = 'resend';
                    $orderReturned->status = 'solved';

                    if($orderReturned->save()){
                        return redirect()->route('shop.orders.pending_groups')->with('success', 'Devolução resolvida com sucesso. Uma nova fatura foi gerada com o valor do frete! Cheque a área de faturas pedentes para realizar o pagamento.');
                    }
                }
            }

            return redirect()->back()->with('error', 'Erro ao salvar resolução, contate a administração.');

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Erro ao salvar resolução, contate a administração.');
        }
        
    }

    public function history(){
        $shop = Auth::guard('shop')->user();

        $ordersService = new OrdersService($shop);
        $orders = $ordersService->getOrdersHistory();

        return view('shop.orders.history', compact('orders'));
    }

    public function show($order_id){
        $shop = Auth::guard('shop')->user();

        $ordersService = new OrdersService($shop);
        $order = $ordersService->find($order_id);

        $variantsService = new ProductVariantsService(null, $shop);
        $variants = $variantsService->get();

        //carrega o valor do dólar
        $dolar_price = CurrencyService::getDollarPrice();        

        return view('shop.orders.show', compact('order', 'variants', 'dolar_price'));
    }

    public function jsonOrder($order_id){
        $shop = Auth::guard('shop')->user();

        $ordersService = new OrdersService($shop);
        $order = $ordersService->find($order_id);

        return $order;
    }

    public function import(){
    	$shop = Auth::guard('shop')->user();
       
    	$result = ShopifyService::getPaidOrdersLimit($shop);
        

        if($result['status'] == 'success'){
            foreach ($result['data'] as $shopify_order) {
               $orders = ShopifyService::registerOrder($shop, $shopify_order);
            // dd($orders);
            }
        }else{
            return redirect()->back()->with($result['status'], $result['message']);
        }

    	return redirect()->route('shop.orders.index')->with('success', 'Pedidos importados com sucesso.');
    }

    public function importWoo(){
    	$shop = Auth::guard('shop')->user();

    	$result = WoocommerceService::getPaidOrders($shop);
        

        if($result['status'] == 'success'){
            foreach ($result['data'] as $woocommerce_order) {
                WoocommerceService::registerOrder($shop, $woocommerce_order);
            }
        }else{
            return redirect()->back()->with($result['status'], $result['message']);
        }

    	return redirect()->route('shop.orders.index')->with('success', 'Pedidos importados com sucesso.');
    }

    public function importCartx(){
    	$shop = Auth::guard('shop')->user();

    	$result = CartxService::getPaidOrders($shop);

        if($result['status'] == 'success'){
            foreach ($result['data'] as $cartx_order) {
                CartxService::registerOrder($shop, $cartx_order);
            }
        }else{
            return redirect()->back()->with($result['status'], $result['message']);
        }

    	return redirect()->route('shop.orders.index')->with('success', 'Pedidos importados com sucesso.');
    }


    public function importYampi(){
    	$shop = Auth::guard('shop')->user();

    	$result = YampiService::getPaidOrders($shop);

        if($result['status'] == 'success'){

            foreach ($result['data'] as $cartx_order) {

                YampiService::registerOrder($shop, $cartx_order);

            }

        }else{

            return redirect()->back()->with($result['status'], $result['message']);

        }


    	return redirect()->route('shop.orders.index')->with('success', 'Pedidos importados com sucesso.');

    }

    public function OrdersCsvImport(Request $request) 
    {
         $shop = Auth::guard('shop')->user();

        $labels = CsvService::storeFiles($request);
		

        $result = CsvService::importCsvOrder($request);
        
	    
       
        $importMsgs = "";
		if (count($result) > 0){
			foreach ($result as $csv_order) {
				$cpf = $csv_order['CPF'];
				$cep = $csv_order['CEP'];
                $rastreameto = $csv_order['Rastreio'];
				$countcpf = strlen($cpf);
				$countcep = strlen($cep);
                $sku =  $csv_order['SKU'];

                $buscproduct = ProductVariants::where('sku', $sku)->first();
                if(!$buscproduct){
                    $buscproduct = Products::where('sku', $sku)->first();

                }
                
                 
                
                
                if(!$csv_order['SKU']){
                return redirect()->route('shop.orders.index')->with('error', 'Erro planilha não foi importada Pedido com sku invalido, Pedido com codigo: '.$csv_order['Código Pedido']);
				break;
                }
                if(!$buscproduct){
                    return redirect()->route('shop.orders.index')->with('error', 'Erro planilha não foi importada Pedido com sku invalido, Pedido com codigo: '.$csv_order['Código Pedido']);
                    break;
                }

				if((!$csv_order['CPF'])) {
				//return $csv_order["Cliente"]." não possui CPF. ";
				return redirect()->route('shop.orders.index')->with('error', 'Erro planilha não foi importada Pedido com cpf invalido, Pedido com codigo: '.$csv_order['Código Pedido']);
				break;
				}
				if((!$csv_order['CEP'])) {
				//return $csv_order["Cliente"]." não possui CPF. ";
				return redirect()->route('shop.orders.index')->with('error', 'Erro planilha não foi importada Pedido com cep invalido, Pedido com codigo: '.$csv_order['Código Pedido']);
				break;
				}
				if(($countcpf <=  9)) { 
				//return $csv_order["Cliente"]." não possui CPF. ";
				return redirect()->route('shop.orders.index')->with('error', 'Erro planilha não foi importada Pedido com cpf invalido, Pedido com codigo: '.$csv_order['Código Pedido']);
				break;
				}
				
				if(($countcep <=  7)) { 
				//return $csv_order["Cliente"]." não possui CPF. ";
				return redirect()->route('shop.orders.index')->with('error', 'Erro planilha não foi importada Pedido com cep invalido: '.$csv_order['Código Pedido']);
				break;
				}


				
			}	
			
		}	
        if(count($result) > 0){
            foreach ($result as $csv_order) {
				//dd($csv_order['CPF']);
				 
                $responseCsvOrder = CsvService::registerOrder($shop, $csv_order, $labels);
                
                $importMsgs .= $responseCsvOrder != true && $responseCsvOrder != '' ? $responseCsvOrder : '';
                
            }
        }else{
            return redirect()->back()->with($result['status'], $result['message']);
        }

        //informa pedidos que deram erro
        if($importMsgs != ""){
            return redirect()->route('shop.orders.index')->with('success', 'Pedidos importados com sucesso.<br />Alguns pedidos deram erro: '.$importMsgs);    
        }
    	return redirect()->route('shop.orders.index')->with('success', 'Pedidos importados com sucesso.');
    }

    public function prepare_payment(Request $request)
    {
        $shop = Auth::guard('shop')->user();
        $order_ids = $request->order_ids;
        $orders = Orders::whereIn('id', $order_ids)->where('shop_id', $shop->id)->get();

        $return = OrdersService::generate_supplier_orders($orders);

        if($return && $return['total_in_supplier_orders']){
            $total_to_pay = $return['total_in_supplier_orders'];

            return $return['order_ids'];
        }else{
            return redirect()->back()->with(['error' => 'Não foi possível pagar, tente novamente.']);
        }
    }

    public function pending_groups(){
        $title = 'Faturas pendentes';
        $shop_id = Auth::guard('shop')->id();
        $groups = SupplierOrderGroup::with('orders')->where('status', 'pending')->where('shop_id', $shop_id)->orderBy('id', 'desc')->get();

        return view('shop.orders.groups', compact('groups', 'title'));
    }

    public function paid_groups(){
        $title = 'Pedidos pagos';
        $shop_id = Auth::guard('shop')->id();
        $groups = SupplierOrderGroup::with('orders')->where('status', 'paid')->where('shop_id', $shop_id)->orderBy('id', 'desc')->get();

        return view('shop.orders.groups', compact('groups', 'title'));
    }

    public function group_detail($id){
        $shop = Auth::guard('shop')->user();
        $admins = Admins::find(2);

        $ordersService = new OrdersService($shop);
        
        if($ordersService->getOrdersInGroup($id) == 0){ //ou  == 0
            $deletedGroup = $ordersService->deletePendingOrderGroup($id);
            if($deletedGroup){
                return redirect(route('shop.orders.pending_groups'));
            }
        }

        $group = SupplierOrderGroup::with('orders')->find($id);
        $payments = OrdersService::getOrderGroupPaymentOptions($group);
        $group_payments = OrderGroupPayments::where('group_id', $id)->get();
        
        
        if($group){            
            return view('shop.orders.group_detail', compact('group', 'payments', 'group_payments', 'admins' ));
        }else{
           return redirect()->back()->with(['error', 'Grupo não encontrado']);
        }
    }

    public function pay_group($group_id, Request $request){
        $shop = Auth::guard('shop')->user();
        $group = SupplierOrderGroup::with('orders')->find($request->group_id);
        $service = new OrdersService($shop);
       

        
       
        $orders = SupplierOrders::where('group_id' , $request->group_id)->first();
        $supplier = Suppliers::where('id' , $orders->supplier_id)->first(); 

    
       

    if (isset($supplier->safe2pay_subaccount_id) and $supplier->safe2pay_subaccount_id != null ){
          if($group){
            //passa o tipo de método de pagamento, pix ou boleto
                $return_data = $service->payGroup($group, 'safe_to_pay', $request->payment_method);

                return redirect()->back()->with([$return_data['status'] => $return_data['message']]);
            }else{
                return redirect()->back()->with(['error' => 'Pagamento não encontrado.']);
            }
    } elseif (isset($supplier->geren_cliente_id) and $supplier->geren_cliente_id != null ){
        
       
       
         if ($shop->responsible_name === null){
            return redirect()->back()->with(['error' => 'Cadastro do Perfil  não esta completo erro no nome.']);

        }
        
        if ($shop->document  === null){
            return redirect()->back()->with(['error' => 'Cadastro do Perfil  não esta completo erro cpf/cnpj.']);

        }

       
               
        $metododepg = $request->payment_method;
        $gerencianet = new Gerencianetpay();      
        
        
        if($metododepg == 'pix'){
            $teste = $gerencianet->pay($supplier , $group , $metododepg , $orders , $shop );
            
            return redirect()->back()->with([$teste['status'] => $teste['message']]);


        }elseif ($metododepg == 'boleto'){
            $teste = $gerencianet->payboleto($supplier , $group , $metododepg , $orders , $shop );
           
            
           
         return redirect()->back()->with([$teste['status'] => $teste['message']]);


        }   
        
    }

    return redirect()->back()->with(['error' => 'Fatura não pode ser Gerada entre em contato com fornecedor .']);
       
    }

    public function pay_group_consulta(Request $request){
       
        $shop = Auth::guard('shop')->user();
        $group = SupplierOrderGroup::where('shop_id', $shop->id)->find($request->grupo);
        $suporders = SupplierOrders::where('group_id' , $request->grupo)->first();
        $supplier = Suppliers::where('id' , $suporders->supplier_id)->first(); 
        $orders = Orders::where('id' , $suporders->order_id)->first();
        $admins = Admins::find(2);

        $gerencianet = new Gerencianetpay(); 
       

       

         if($group->paid_by == 'pix'){
          
            $teste = $gerencianet->consultapix($supplier , $group , $orders , $shop );
            
            $resultpix['dados'] = $teste; 
            $resultpix['dados'] = $teste; 
            $resultpix['sucess'] = false;
            $resultpix['message'] = 'OK';
        
            if (($teste == 'CONCLUIDA') and ($group->status <> 'paid') ){

            $group->status = 'paid'; 
            $group->save(); 
            $suporders->status =  'paid';
            $suporders->save();
            $orders->status =  'paid';
            $orders->save();

            if($admins->citel == 1 ){
            $order = Orders::where('id' ,$suporders->order_id )->first();
            $customers = Customers::where('id' ,$order->customer_id )->first();
            $customersandress = CustomerAddresses::where('customer_id', $customers->id)->first();

            $orderitems = OrderItems::where('order_id' ,$suporders->order_id )->get();
            
            $consultaclientecitel = CitelService::getConsCliente($customers->cpf);
            
            if($consultaclientecitel['status'] == '200'){

               $dadoscliente =  $consultaclientecitel['resposta'];
               
               $codclientecitel = $dadoscliente->codigoCliente;
               $ordercitel = CitelService::getPaidOrders($codclientecitel, $orderitems, $order );
               
            }else {
                $cadclientecitel = CitelService::getCadCliente($customers , $customersandress );
                
                 if ($cadclientecitel['status'] == '200'){
                    $dadoscliente =  $consultaclientecitel['resposta'];
               
                    $codclientecitel = $dadoscliente->codigoCliente;
                    $ordercitel = CitelService::getPaidOrders( $codclientecitel, $orderitems, $order );
                    

                 }
                    
            }
        }


            
        }
            echo json_encode($resultpix); 
          


           }


        
        
       
    }    

    public function cancel($order_id){
        $order = Orders::find($order_id);

        if($order){
            $cancelamento = OrdersService::cancelOrder($order);
            return redirect(route('shop.orders.index'))->with([$cancelamento['status'] => $cancelamento['message']]);
        }else{
            return redirect(route('shop.orders.index'))->with(['error' => 'Pedido não encontrado.']);
        }
    }

    public function uploadReceipt($order_id, Request $request){
        $shop = Auth::guard('shop')->user();
        $order = Orders::with('customer')->where('shop_id', $shop->id)->find($order_id);

        if($request->hasFile('customer_receipt')){
            $receipt = new Receipts();

            $name = Str::random(15).$shop->id . '.' . $request->customer_receipt->extension();
            $path = $request->customer_receipt->storeAs('receipts', $name, 's3');

            $file_name = env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/').$path;

            $receipt->shop_id = $shop->id;
            $receipt->customer_id = $order->customer_id;
            $receipt->type = 'order';
            $receipt->to = 'customer';
            $receipt->file = $file_name;
            $receipt->total_amount = $order->amount;

            if($receipt->save()){
                foreach($order->supplier_orders as $supplier_order){
                    $receipt_order = new ReceiptOrders();

                    $receipt_order->receipt_id = $receipt->id;
                    $receipt_order->supplier_order_id = $supplier_order->id;
                    $receipt_order->order_id = $order->id;

                    $receipt_order->save();
                }
            }

            OrdersService::sendReceiptToCustomer($order, $receipt);
        }

        return redirect()->back()->with('success', 'Notas fiscal enviada com sucesso.');
    }

    public function downloadReceipt($receipt_id){
        $shop = Auth::guard('shop')->user();
        $receipt = Receipts::where('shop_id', $shop->id)->find($receipt_id);

        if(!$receipt){
            return redirect()->back()->with('error', 'Você não tem permissão para efetuar o download desta nota fiscal.');
        }

        $file_name = str_replace(env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/'), '', $receipt->file);

        return Storage::disk('s3')->download($file_name);
    }

    public function applyDiscount($group_id, Request $request){
        $code = $request->code;
        $coupon = Discounts::where('code', $code)->first();
        $supplier_orders = SupplierOrders::with('items')->with('supplier')->where('group_id', $group_id)->get();
        $can_use = false;

        if($coupon){
            $already_used = SupplierOrdersDiscounts::where('discount_id', $coupon->id)->where('group_id', $group_id)->first();
            if(!$already_used){
                foreach($supplier_orders as $so){
                    $service = new SupplierOrdersService($so->supplier);
                    
                    foreach($so->items as $item){
                        if(strtoupper(substr($coupon->code, -3)) == 'ALL'){ //aplica independente da variante
                            $item->amount = $item->amount - ($item->amount * ($coupon->percentage / 100));
                            $item->save();
                            $can_use = true;
                        }else{ //aplica só naquela variante
                            if($item->product_variant_id == $coupon->variant_id){                            
                                $item->amount = $item->amount - ($item->amount * ($coupon->percentage / 100));
                                $item->save();
                                $can_use = true;
                            }
                        }                        
                    }
                    $service->recalcSupplierOrderAmount($so);
                }

                if($can_use){
                    SupplierOrdersDiscounts::create([
                        'group_id' => $group_id,
                        'discount_id' => $coupon->id
                    ]);

                    return redirect()->back()->with(['success' => 'Cupom adicionado com sucesso']);
                }else{
                    return redirect()->back()->with(['error' => 'Este cupom não pode ser usado nesse pedido.']);
                }
            }else{
                return redirect()->back()->with(['error' => 'Cupom já utilizado neste pedido.']);
            }
        }else{
            return redirect()->back()->with(['error' => 'Cupom não encontrado.']);
        }
    }

    public function updateCustomer($order_id, Request $request){
        $shop = Auth::guard('shop')->user();

        $order = Orders::where('shop_id', $shop->id)->find($order_id);

        if(!$order || !$order->customer || !$order->customer->address){
            return redirect()->back()->with('error', 'Você não pode alterar o endereço deste cliente.');
        }

        $order->customer->address->address1 = $request->address1;
        $order->customer->address->address2 = $request->address2;
        $order->customer->address->company = $request->company;
        $order->customer->address->zipcode = $request->zipcode;
        $order->customer->address->city = $request->city;
        $order->customer->address->province = $request->province;

        $order->customer->address->save();

        return redirect()->back()->with('success', 'Endereço do cliente alterado com sucesso.');
    }

    public function addItem($order_id, Request $request){
        $shop = Auth::guard('shop')->user();

        $order = Orders::where('shop_id', $shop->id)->find($order_id);

        if(!$order){
            return redirect()->back()->with('error', 'Você não pode adicionar este produto ao pedido.');
        }

        // Verifica se esse não é o último produto do pedido.
        if(!is_numeric($request->quantity) || $request->quantity <= 0){
            return redirect()->back()->with('error', 'A quantidade é obrigatória.');
        }

        $variantsService = new ProductVariantsService(null, $shop);
        $variant = $variantsService->find($request->variant_id);

        if(!$variant){
            return redirect()->back()->with('error', 'Você não pode adicionar este produto ao pedido.');
        }

        $item = new OrderItems();
        $item->order_id = $order_id;
        $item->product_variant_id = $variant->id;
        $item->external_service = 'shopify';
        $item->external_price = $request->external_price;
        $item->sku = $variant->sku;
        $item->title = $variant->title;
        $item->quantity = $request->quantity;
        $item->amount = $variant->price * $request->quantity;
        $item->charge = 1;

        $item->save();

        // Adiciona o novo valor destes produtos ao pedido
        $item->order->items_amount += $item->amount;
        $item->order->amount += $item->amount;
        $item->order->save();

        return redirect()->back()->with('success', 'Pedido alterado com sucesso.');
    }

    public function updateItem($item_id, Request $request){
        $shop = Auth::guard('shop')->user();

        $item = OrderItems::whereHas('order', function($q) use ($shop){
            $q->where('shop_id', $shop->id);
        })->find($item_id);

        if(!$item || !$item->variant){
            return redirect()->back()->with('error', 'Você não pode alterar este produto.');
        }

        // Verifica se esse não é o último produto do pedido.
        if(!is_numeric($request->quantity) || $request->quantity <= 0){
            return redirect()->back()->with('error', 'A quantidade é obrigatória.');
        }

        $unit_price = $item->amount / $item->quantity;

        // Retira o valor atual destes produtos no pedido
        $item->order->items_amount -= $item->amount;
        $item->order->amount -= $item->amount;

        // Recalcula o valor destes produtos e redefine o valor externo
        $item->quantity = $request->quantity;
        $item->external_price = $request->external_price;
        $item->amount = $unit_price * $request->quantity;

        // Adiciona o novo valor destes produtos ao pedido
        $item->order->items_amount += $item->amount;
        $item->order->amount += $item->amount;

        $item->save();
        $item->order->save();

        return redirect()->back()->with('success', 'Pedido alterado com sucesso.');
    }

    public function removeItem($item_id, Request $request){
        $shop = Auth::guard('shop')->user();

        $item = OrderItems::whereHas('order', function($q) use ($shop){
            $q->where('shop_id', $shop->id);
        })->find($item_id);

        if(!$item || !$item->variant){
            return redirect()->back()->with('error', 'Você não pode remover este produto.');
        }

        // Verifica se esse não é o último produto do pedido.
        if($item->order->items->count() <= 1){
            return redirect()->back()->with('error', 'Você não pode remover todos os produtos do pedido. Para cancelar o pedido utilize o botão "Cancelar Pedido" no final da página de detalhes do pedido.');
        }

        // Retira o valor do produto do pedido
        $item->order->items_amount -= $item->amount;
        $item->order->amount -= $item->amount;
        $item->order->save();

        // Remove o produto do pedido
        $item->delete();

        return redirect()->back()->with('success', 'Produto removido com sucesso.');
    }


    public static function calculateShipping($items, $supplier, $customer, $shop = NULL, $order = NULL){
        try{
            //caso seja o pessoal da s2m2, verifica o método de envio dos china
            if($supplier->id == 56){
                $chinaShippingService = new ChinaShippingService();

                $address = $customer->address;
                
                $products = $chinaShippingService->prepareOrderProducts($items);

                $chinaShippingService->setToZipcode($address->zipcode);
                $chinaShippingService->calcBoxWeight($products);

                $valor = $chinaShippingService->getShippingPrice();
                
                if($valor && $valor > 0){
                    return $valor;
                }
            }

            if($supplier->shipping_method == 'correios' && $supplier->correios_settings){
                
                $address = $customer->address;
                $products = CorreiosService::prepareOrderProducts($items);

                $correiosService = new CorreiosService();

                $correiosService->setFromZipcode($supplier->address->zipcode);
                $correiosService->setToZipcode($address->zipcode);
                $correiosService->calcBoxSize($products);

                $result = $correiosService->getShippingPrices($supplier);

                if($result->pac && $result->pac > 0){
                    return $result->pac * ($supplier->correios_settings->percentage / 100);
                }
            }

            if($supplier->shipping_method == 'total_express' && $supplier->total_express_settings){
                
                $address = $customer->address;
                $products = TotalExpressService::prepareOrderProducts($items);

                $totalExpressService = new TotalExpressService($supplier->total_express_settings);

                $totalExpressService->setToZipcode($address->zipcode);
                $totalExpressService->calcBoxSize($products);

                $valor = $totalExpressService->getValorServico();

                if($valor && $valor > 0){
                    return $valor;
                }
            }

            if($supplier->shipping_method == 'melhor_envio' /*&& $supplier->melhor_envio_settings*/){
                $address = $customer->address;
                
                $melhorEnvioService = new MelhorEnvioService();
                $melhorEnvioService->setFromZipcode($supplier->address->zipcode);
                $melhorEnvioService->setToZipcode($address->zipcode);
                $melhorEnvioService->prepareOrderProducts($items);
                
                //só retorna o valor da cotacao
                $valor = $melhorEnvioService->quoteFreightMinValue();

                if($valor && $valor > 0){
                    return $valor;
                }
            }
        }catch(\Exception $e){
            report($e);
        }

        return 0;
    }
    
    public function importBling(){
        $shop = Auth::guard('shop')->user();
        
      

    	$result = BlingServiceShop::getPaidOrders($shop);
    	
        if($result['status'] == '403'){
            return redirect()->back()->with('error', $result['message']); 
        }    

        if($result['status'] == '200'){
            foreach ($result['data'] as $bling_order) {
                
                $bling_itens = $bling_order->pedido->itens[0]->item->codigo;
                $bling_status = $bling_order->pedido->situacao;
                if (($bling_itens) and ($bling_status == 'Em aberto')){
                    $products = Products::where('sku' , $bling_itens)->first();
                    if($products){
                        BlingServiceShop::registerOrder($shop, $bling_order);
                    }
                    
                }
                
            }
        }else{
            return redirect()->back()->with($result['status'], $result['message']);
        }

    	return redirect()->route('shop.orders.index')->with('success', 'Pedidos importados com sucesso.');
    }

public function updateOrderTrackingNumberBling(Request $request , $order_id){
        //recebe um vetor de orders id e verifica no bling se esse id tem o número de rastreio, se tiver, salva esse número
        $shop = Auth::guard('shop')->user();
        $blingkey = $shop->bling_apikey;        
        if($request->order_id != ''){         
            


            $bling_service = new BlingServiceShop($order_id , $blingkey);            
            $rastreamentobling = $bling_service->checkOrderTrackingNumber($order_id ,  $blingkey);            
            
           
            if ($rastreamentobling == null){
                return redirect()->route('shop.orders.index')->with('error', 'Pedido sem Rastreamento no Bling.');
             } else {
                $rastreamento = $rastreamentobling->codigoRastreamento;

             }

            if($rastreamento != ''){
              
                $orders = new Orders();
                $resp = $orders->where('id', $order_id)->first();
                $resp-> tracking_url = $rastreamentobling->urlRastreamento;     
                $resp-> tracking_number = $rastreamentobling->codigoRastreamento;  
                $resp-> tracking_servico = $rastreamentobling->servico;  
                $resp-> shipping_amount  = '0.00';
                $resp->  amount  = $resp->items_amount;
                $resp->save();
                $ordersshiping = new OrderShippings();
                $respship = $ordersshiping->where('order_id', $order_id)->first();
                $respship->  amount  = '0.00';
                $respship->save();


                return redirect()->route('shop.orders.index')->with('success', 'Rastreamento importado com sucesso.');

            } else {
                return redirect()->route('shop.orders.index')->with('error', 'Pedido sem Rastreamento no Bling.');



            }    

            
           
        }
    }       
    
    
    public function importOrderMercadolivre()
    {
     //   return redirect()->route('shop.orders.index')->with('success', 'Sem pedidos para importar.');
     
        $shop = Auth::guard('shop')->user();
        $mytime = date('Y-m-d H:i:s');
        
       
        $apimercadolivre = Mercadolivreapi::where('shop_id',$shop->id )->first();
       
        $order_ml =   MercadolivreService::getOrder($apimercadolivre);
       
     	
        if ($apimercadolivre->seller_id_ml <> null){
         //   dd($order_ml);
        
        if ($order_ml['status'] == '401'){

            $tokenml = MercadolivreService::getToken($shop, $apimercadolivre );
            $token = Mercadolivreapi::where('shop_id',$shop->id)->first();
            $token->token = $tokenml;
            $token->token_exp = date($mytime, strtotime('+4 Hours'));
            $token->save();

            $order_ml =   MercadolivreService::getOrder($apimercadolivre);
           // dd($order_ml);


        }

        if ($apimercadolivre->seller_id_ml == null){
            //dd($order_ml);
        if ($order_ml['status'] == '401'){

            $tokenml = MercadolivreService::getToken($shop, $apimercadolivre );
            $token = Mercadolivreapi::where('shop_id',$shop->id)->first();
            $token->token = $tokenml;
            $token->token_exp = date($mytime, strtotime('+4 Hours'));
            $token->save();

            $order_ml =   MercadolivreService::getOrder($apimercadolivre);
            //dd($order_ml);


        }
    }


       
        foreach($order_ml['order']->results as $order){
        //dd($order);
		//if($order->order_items[0]->item->seller_sku == "STO315") {
		//	dd($order);
		//}	
		$ordertag = $order->tags[0];
		$idorder =  $order->order_items[0]->item->id;
        $sku = $order->order_items[0]->item->seller_sku;
        $variant = ProductVariants::where('sku', $sku)->first();
		
		if ($variant){
		$shopproduct = ShopProducts::where('product_id', $variant->product_id)->first();
		
		if($shopproduct){
			if ($ordertag == "not_delivered" ){
				
                           
              MercadolivreService::registerOrder($shop, $order);
             

			}	
			
			
			
		}	
		}

       
        if($order){
            $orderat =  Orders::where('external_id' , $order->id)->first(); 
            
            $gerorder =  MercadolivreService::getAnuncio($apimercadolivre , $orderat );
            
            if ($gerorder['status'] == 200){  
                if(isset($gerorder['anuncio']->tracking_number)){
                    $orderat->tracking_number = $gerorder['anuncio']->tracking_number;
                    $orderat->tracking_servico = $gerorder['anuncio']->tracking_method;
                    $orderat->save();

                }               
                
             }
            }   







        }    

    }else{
        if ($order_ml['status']== '401'){

            $tokenml = MercadolivreService::getToken($shop, $apimercadolivre );
            $token = Mercadolivreapi::where('shop_id',$shop->id)->first();
            $token->token = $tokenml;
            $token->token_exp = date($mytime, strtotime('+4 Hours'));
            $token->save();
            $order_ml =   MercadolivreService::getOrder($apimercadolivre);
            return redirect()->route('shop.orders.index')->with('erro', 'Atualizamos o token importe novamente seus pedidos ML.');

        }

    }     




        
            return redirect()->route('shop.orders.index')->with('success', 'Pedidos importados com sucesso.');
       
    }


    public function importOrderMercadolivre2()
    {
        
        $shop = Auth::guard('shop')->user();
        $apimercadolivre = Mercadolivreapi::where('shop_id',$shop->id )->first();
		 $mytime = date('Y-m-d H:i:s');
     	
        if ($apimercadolivre) {
		if ($apimercadolivre->seller_id_ml <> null){
        $order_ml =   MercadolivreService::getOrder($apimercadolivre);
        //dd($order_ml);
      
        
                
        if ($order_ml['status'] == '401'){

            $tokenml = MercadolivreService::getToken($shop, $apimercadolivre );
            $token = Mercadolivreapi::where('shop_id',$shop->id)->first();
            $token->token = $tokenml;
            $token->token_exp = date($mytime, strtotime('+4 Hours'));
            $token->save();

            $order_ml =   MercadolivreService::getOrder($apimercadolivre);


        }

        if ($order_ml['status'] == '403'){
            return redirect()->route('shop.orders.index')->with('erro', 'Não Conseguimos buscar sua vendas ML, entre em contato com o suporte.');
         
        }
			
        if ($order_ml['status'] == 'error'){

            $tokenml = MercadolivreService::getToken($shop, $apimercadolivre );
            $token = Mercadolivreapi::where('shop_id',$shop->id)->first();
            $token->token = $tokenml;
            $token->token_exp = date($mytime, strtotime('+4 Hours'));
            $token->save();

            $order_ml =   MercadolivreService::getOrder($apimercadolivre);


        }
		
		
          if ($order_ml['status'] == 200) {	
              $order_mecl = $order_ml['order']->results;
             //dd($order_mecl[0]);
    
              foreach($order_mecl as $order_ml){
                $idorder =  $order_ml->order_items[0]->item->id;
                $sku = $order_ml->order_items[0]->item->seller_sku;
                $variant = ProductVariants::where('sku', $sku)->first();
               
                if($variant){
                    //$order_ml = $order;
                    MercadolivreService::registerOrder($shop, $order_ml);
    
                }
                
              }  
          }  
        }
            }
        
         
        
    }

    
}
