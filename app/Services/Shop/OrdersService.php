<?php

namespace App\Services\Shop;

use App\Mail\ReceiptMail;
use App\Mail\ProcessedOrder;
use App\Mail\NewOrderRequest;
use App\Models\Discounts;
use App\Models\Shops;
use App\Models\Orders;
use App\Models\SupplierOrderGroup;
use App\Models\SupplierOrderItems;
use App\Models\SupplierOrders;
use App\Models\SupplierOrderShippings;
use App\Models\Suppliers;
use App\Models\FreteMelhorEnvio;
use App\Models\ProductVariants;
use App\Models\Products;
use App\Models\OrderReturned;
use App\Services\CorreiosService;
use App\Services\PaymentsService;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Services\CurrencyService;
use App\Services\MelhorEnvioService;
use App\Exceptions\CustomException;

class OrdersService{

	public $shop;

	public function __construct(Shops $shop){
		if(!$shop){
			throw new CustomException("Aconteceu algum erro inesperado ao processar esta requisição.", 500);
		}

		$this->shop = $shop;
	}

	public function dashboardData(){
        $total_pending = Orders::where('shop_id', $this->shop->id)->where('status', 'pending')->where('supplier_order_created', 0)->orderBy('id', 'desc')->sum('amount');
        $total_cost = Orders::where('shop_id', $this->shop->id)->where('status', 'paid')->orderBy('id', 'desc')->sum('amount');
        $total_earning = Orders::where('shop_id', $this->shop->id)->orderBy('id', 'desc')->sum('external_price');
        $profit = $total_earning - $total_cost;

        return compact('total_pending', 'total_cost', 'total_earning', 'profit');
    }

    public function clearNoCustomerOrders(){
        Orders::doesntHave('customer')->update(['external_id' => null]);
        Orders::doesntHave('customer')->delete();
    }

	public function getPendingOrders($limit = null){
        $orders = Orders::where('shop_id', $this->shop->id)
                    ->where('status', 'pending')
                    ->where('supplier_order_created', 0)
                    ->orderBy('id', 'desc')
                    ->paginate(100);

                    
		// if($limit){
		//     $orders->limit($limit);
        // }

        // return $orders->get();
      
        return $orders;

    }

    public function deletePendingOrderGroup($group_id){
        try {
            //só deleta ordem se ela estiver pendente
            $order = SupplierOrderGroup::where('id', $group_id)
                                    ->where('shop_id', $this->shop->id)
                                    ->where('status', 'pending')
                                    ->first();

            //seleciona todas as supplierOrders dessa ordem e deleta
            if($order){
                $supplierOrders = SupplierOrders::where('group_id', $order->id)
                                                ->where('status', 'pending')
                                                ->get();

                if($supplierOrders){
                    foreach ($supplierOrders as $supplierOrder) {
                        //marca a ordem dessa supplier order com supplier_order_created = 0, para indicar que não criou a supplier order dela ainda
                        $supplierOrder->order->supplier_order_created = 0;
                        $supplierOrder->order->save();

                        $supplierOrder->delete();
                    }
                }

                if($order->delete()){ //deleta o grupo
                    return TRUE;
                }
            }

            return FALSE;

        } catch(\Exception $e){
            report($e);
            return FALSE;
        }
    }

    public function deletePendingOrderInGroup($group_id, $order_id){
        try {
            //só deleta ordem se ela estiver pendente
            $order = SupplierOrderGroup::where('id', $group_id)
                                    ->where('shop_id', $this->shop->id)
                                    ->where('status', 'pending')
                                    ->first();
            if($order){
                $supplierOrder = SupplierOrders::where('group_id', $order->id)
                                                ->where('order_id', $order_id)
                                                ->where('status', 'pending')
                                                ->first();
                if($supplierOrder){ //caso a ordem do fornecedor exista
                    $supplierOrder->order->supplier_order_created = 0;
                    $supplierOrder->order->save();

                    $supplierOrder->delete();

                    //caso de certo, exclui o pix ou boleto se existirem
                    $order->description = null;
                    $order->payment_json = null;
                    $order->transaction_id = null;
                    $order->bankslip_url = null;
                    $order->bankslip_digitable_line = null;
                    $order->bankslip_barcode = null;
                    $order->bankslip_duedate = null;
                    $order->status_pix = null;
                    $order->message_pix = null;
                    $order->description_pix = null;
                    $order->qrcode_pix = null;
                    $order->key_pix = null;
                    $order->payment_json_pix = null;
                    $order->transaction_id_pix = null;
                    $order->paid_by = null;
                    $order->save();

                    return TRUE;
                }
            }

            return FALSE;

        } catch(\Exception $e){
            report($e);
            return FALSE;
        }
    }

    public function getOrdersInGroup($group_id){
        try {
            //só deleta ordem se ela estiver pendente
            $order = SupplierOrderGroup::where('id', $group_id)
                                    ->where('shop_id', $this->shop->id)
                                    ->where('status', 'pending')
                                    ->first();

            //seleciona todas as supplierOrders dessa ordem e deleta
            if($order){
                $supplierOrders = SupplierOrders::where('group_id', $order->id)
                                                ->where('status', 'pending')
                                                ->count();
                return $supplierOrders;
            }

            return 0;

        } catch(\Exception $e){
            report($e);
            return 0;
        }
    }

    public function getPendingOrdersSearch($querySearch, $filter){
        //faz a query de acordo com o filtro passado
        if($filter == 'customer'){
            //adiciona os dados do lojista a busca
            $orders = Orders::where('shop_id', $this->shop->id)
                ->where('status', 'pending')
                ->where('supplier_order_created', 0)
                ->whereHas('customer', function ($query) use ($querySearch) {
                    $query->where('first_name', 'like', '%'.$querySearch.'%')//nome do cliente
                            ->orWhere('last_name', 'like', '%'.$querySearch.'%') //ultimo nome
                            ->orWhere('email', 'like', '%'.$querySearch.'%'); //email
                })
                ->select('orders.*')
                ->orderBy('id', 'desc')
                ->paginate(100);

        }else if($filter == 'created_at' || $filter == 'external_created_at'){
            $orders = Orders::where('shop_id', $this->shop->id)
                    ->where('status', 'pending')
                    ->where('supplier_order_created', 0)
                    ->where($filter, 'like', '%'.implode("-",array_reverse(explode("/", $querySearch))).'%')
                    ->orderBy('id', 'desc')
                    ->paginate(100);
        }else{
            $orders = Orders::where('shop_id', $this->shop->id)
                    ->where('status', 'pending')
                    ->where('supplier_order_created', 0)
                    ->where($filter, 'like', '%'.$querySearch.'%') //filtro
                    ->orderBy('id', 'desc')
                    ->paginate(100);
        }

        return $orders;
    }

    public function getSentOrdersSearch($querySearch, $filter){
        //faz a query de acordo com o filtro passado
        if($filter == 'customer'){
            //adiciona os dados do lojista a busca
            $orders = Orders::where('shop_id', $this->shop->id)
                ->whereHas('supplier_order.shipping', function($q){
                    $q->where('status', 'sent');
                })
                ->whereHas('customer', function ($query) use ($querySearch) {
                    $query->where('first_name', 'like', '%'.$querySearch.'%')//nome do cliente
                            ->orWhere('last_name', 'like', '%'.$querySearch.'%') //ultimo nome
                            ->orWhere('email', 'like', '%'.$querySearch.'%'); //email
                })
                ->select('orders.*')
                ->orderBy('id', 'desc')
                ->paginate(100);

        }else if($filter == 'created_at' || $filter == 'external_created_at'){
            $orders = Orders::where('shop_id', $this->shop->id)
                    ->whereHas('supplier_order.shipping', function($q){
                        $q->where('status', 'sent');
                    })
                    ->where($filter, 'like', '%'.implode("-",array_reverse(explode("/", $querySearch))).'%')
                    ->orderBy('id', 'desc')
                    ->paginate(100);
        }else{
            $orders = Orders::where('shop_id', $this->shop->id)
                    ->whereHas('supplier_order.shipping', function($q){
                        $q->where('status', 'sent');
                    })
                    ->where($filter, 'like', '%'.$querySearch.'%') //filtro
                    ->orderBy('id', 'desc')
                    ->paginate(100);
        }

        return $orders;
    }

    public function getTotalCountPendingOrders(){
        $countOrders = Orders::where('shop_id', $this->shop->id)
                    ->where('status', 'pending')
                    ->where('supplier_order_created', 0)
                    ->orderBy('id', 'desc')
                    ->count();

		// if($limit){
		//     $orders->limit($limit);
        // }

        // return $orders->get();
        return $countOrders;
    }

    public function getPaidOrders(){
        return Orders::where('shop_id', $this->shop->id)->where('status', 'paid')->orderBy('id', 'desc')->get();
    }

    public function getReturnedOrders(){
        return OrderReturned::where('shop_id', $this->shop->id)->orderBy('id', 'desc')->get();
    }

    public function getSentOrders(){
        return Orders::where('shop_id', $this->shop->id)
            ->whereHas('supplier_order.shipping', function($q){
                $q->where('status', 'sent');
            })->orderBy('id', 'desc')->paginate(100);
    }

    public function getDeliveredSupplierOrders(){
        return SupplierOrders::whereHas('shipping', function($q){
            $q->where('status', 'completed');
        })->whereHas('order', function($q){
            $q->where('shop_id', $this->shop->id);
        })->orderBy('id', 'desc')->get();
    }

	public function find($order_id){
    	$order = Orders::with('items', 'customer', 'receipts')->where('shop_id', $this->shop->id)->find($order_id);

    	if(!$order){
    		throw new CustomException("Aconteceu algum erro inesperado ao processar essa requisição. Tente novamente em alguns minutos.", 500);
    	}
    	return $order;
    }

    public function findReturnedOrder($order_id){
    	$order = OrderReturned::where('shop_id', $this->shop->id)->find($order_id);

    	if(!$order){
    		throw new CustomException("Aconteceu algum erro inesperado ao processar essa requisição. Tente novamente em alguns minutos.", 500);
    	}
    	return $order;
    }

    public function getOrdersHistory(){
		return Orders::where('shop_id', $this->shop->id)->where('status', '!=', 'pending')->orderBy('id', 'desc')->get();
    }

    public static function getOrderGroupPaymentOptions($group){
        $suppliers = Suppliers::whereIn('id', $group->orders->pluck('supplier_id'))->get();

        $payment_array = [];

        foreach($suppliers as $supplier){
            $payment_array[] = [
                'supplier' => $supplier,
                'orders' => $group->orders->where('supplier_id', $supplier->id)
            ];
        }

        return $payment_array;
    }

    public static function cancelOrder($order){
	    if($order->status != 'canceled'){
            if($order->supplier_orders->count() < 1){
                $order->status = 'canceled';
                $order->save();
                return ['status' => 'success', 'message' => 'Pedido cancelado com sucesso.'];
            }else{
                return ['status' => 'error', 'message' => 'Este pedido já foi enviado ao fornecedor e não pode ser cancelado.'];
            }
        }else{
            return ['status' => 'error', 'message' => 'Este pedido já foi cancelado'];
        }

    }

    public static function real_measurement($order_item){
        $cm_cubico = $order_item->quantity * ($order_item->variant->width * $order_item->variant->height * $order_item->variant->depth);
        $raiz_cubica = round(pow($cm_cubico, 1/3), 2);

        return $raiz_cubica;
    }

    public static function generate_supplier_orders($orders){
        $suppliers = [];
        $total_to_pay = 0;

        $dolar_price = CurrencyService::getDollarPrice();

        $group = new SupplierOrderGroup();
        $group->shop_id = Auth::guard('shop')->id();
        $group->description = date('d/m/Y H:i:s');
        $group->status = 'pending';
        $group->save();

        foreach($orders as $order){
            if($order->supplier_order_created == 0) {
                foreach ($order->items as $item) {
                    if($item->variant && $item->variant->product && $item->variant->product->supplier){
                        $suppliers[$item->variant->product->supplier->id][$item->order_id][] = $item;
                    }else{ //caso a variante tenha sido excluida
                        $variant = ProductVariants::withTrashed()->find($item->product_variant_id);

                        if($variant){
                            $product = Products::withTrashed()->find($variant->product_id);

                            if($product){
                                $supplier = Suppliers::withTrashed()->find($product->supplier_id);
                                if($supplier){
                                    $suppliers[$supplier->id][$item->order_id][] = $item;
                                }
                            }
                        }
                    }
                }

                $order->supplier_order_created = 1;
                $order->save();
            }
        }

        foreach($suppliers as $supplier_id => $supplier_order){
            foreach($supplier_order as $order_id => $items){
                $new_order = new SupplierOrders();
                $new_order->order_id = $order_id;
                $new_order->supplier_id = $supplier_id;
                $new_order->status = 'pending';
                $new_order->group_id = $group->id;

                $new_order->save();

                $order = Orders::with('shippings')->find($order_id);

                $total_shipping = $order->shippings->where('supplier_id', $supplier_id)->sum('amount');

                $shipping = new SupplierOrderShippings();
                $shipping->supplier_id = $supplier_id;
                $shipping->supplier_order_id = $new_order->id;
                $shipping->amount = $total_shipping;
                $shipping->status = 'pending';
                $shipping->external_service = $order->external_service;
                $shipping->tracking_url = $order->tracking_url;
                $shipping->tracking_number =  $order->tracking_number;
                $shipping->company = $order->tracking_servico;
                $shipping->save();

                $total_amount = 0;

                foreach($items as $item){
                    //$discount = Discounts::where('variant_id', $item->product_variant_id)->first();
                    $discount = false;

                    $new_item = new SupplierOrderItems();
                    $new_item->supplier_order_id = $new_order->id;
                    $new_item->product_variant_id = $item->product_variant_id;

                    //antes de atribuir o preço do item, verificar se a variant ta em dólar
                    //precisa realizar a cotação e multiplicar
                    if($item->variant && $item->variant->product){
                        if($item->variant->product->currency == 'US$'){

                            if(isset($dolar_price['price'])){
                                $amount = $item->amount * $dolar_price['price'];
                            }else{
                                $amount = $item->amount * 1000;
                            }
                        }else{
                            $amount = $item->amount;
                        }

                        if($discount){
                            $new_item->amount = $amount - ($amount * ($discount->percentage/100));
                        }else{
                            $new_item->amount = $amount;
                        }

                        //caso seja um produto da s2m2, ainda tem q multiplicar por 1.05 para adicionar mais 5% de mão de obra no item
                        if($item->variant->product->supplier->id == 56){
                            $new_item->amount = $new_item->amount * 1.05;
                        }

                        $new_item->quantity = $item->quantity;

                        $new_item->save();

                        $total_amount += $new_item->amount;
                    }else{ //caso a variante tenha sido excluida
                        $variant = ProductVariants::withTrashed()->find($item->product_variant_id);

                        if($variant){
                            $product = Products::withTrashed()->find($variant->product_id);

                            if($product){
                                if($product->currency == 'US$'){

                                    if(isset($dolar_price['price'])){
                                        $amount = $item->amount * $dolar_price['price'];
                                    }else{
                                        $amount = $item->amount * 1000;
                                    }
                                }else{
                                    $amount = $item->amount;
                                }

                                if($discount){
                                    $new_item->amount = $amount - ($amount * ($discount->percentage/100));
                                }else{
                                    $new_item->amount = $amount;
                                }

                                //caso seja um produto da s2m2, ainda tem q multiplicar por 1.05 para adicionar mais 5% de mão de obra no item
                                if($product->supplier->id == 56){
                                    $new_item->amount = $new_item->amount * 1.05;
                                }

                                $new_item->quantity = $item->quantity;

                                $new_item->save();

                                $total_amount += $new_item->amount;
                            }
                        }
                    }

                }

                //$total_measurement - 100
                //$centimeters_supplier - X
                //$total * x = $cent_suplier * 100
                //x = $cent_suplier * 100 / $total

                $new_order->amount = $total_amount;
                $new_order->total_amount = $total_amount + $total_shipping;

                $new_order->save();
            }
        }

        $total_to_pay = SupplierOrders::whereIn('order_id', $orders->pluck('id'))->sum('total_amount');

        return ['total_in_supplier_orders' => $total_to_pay, 'order_ids' => $orders->pluck('id')];
    }

    public static function generate_supplier_orders_individual_order($order){
        $suppliers = [];
        $total_to_pay = 0;

        $dolar_price = CurrencyService::getDollarPrice();

        $group = new SupplierOrderGroup();
        $group->shop_id = Auth::guard('shop')->id();
        $group->description = date('d/m/Y H:i:s');
        $group->status = 'pending';
        $group->save();

        if($order->supplier_order_created == 0) {
            foreach ($order->items as $item) {
                if($item->variant && $item->variant->product && $item->variant->product->supplier){
                    $suppliers[$item->variant->product->supplier->id][$item->order_id][] = $item;
                }else{ //caso a variante tenha sido excluida
                    $variant = ProductVariants::withTrashed()->find($item->product_variant_id);

                    if($variant){
                        $product = Products::withTrashed()->find($variant->product_id);

                        if($product){
                            $supplier = Suppliers::withTrashed()->find($product->supplier_id);
                            if($supplier){
                                $suppliers[$supplier->id][$item->order_id][] = $item;
                            }
                        }
                    }
                }
            }

            $order->supplier_order_created = 1;
            $order->save();
        }

        foreach($suppliers as $supplier_id => $supplier_order){
            foreach($supplier_order as $order_id => $items){
                $new_order = new SupplierOrders();
                $new_order->order_id = $order_id;
                $new_order->supplier_id = $supplier_id;
                $new_order->status = 'pending';
                $new_order->group_id = $group->id;

                $new_order->save();

                $orderAux = Orders::with('shippings')->find($order_id);
                //dd($orderAux->shippings);
                $total_shipping = $orderAux->shippings->where('supplier_id', $supplier_id)->sum('amount');

                //dd($total_shipping);

                $shipping = new SupplierOrderShippings();
                $shipping->supplier_id = $supplier_id;
                $shipping->supplier_order_id = $new_order->id;
                $shipping->amount = $total_shipping;
                $shipping->status = 'pending';
                $shipping->external_service = $orderAux->external_service;
                $shipping->save();

                $total_amount = 0;

                foreach($items as $item){
                    //$discount = Discounts::where('variant_id', $item->product_variant_id)->first();
                    //$discount = false;

                    $new_item = new SupplierOrderItems();
                    $new_item->supplier_order_id = $new_order->id;
                    $new_item->product_variant_id = $item->product_variant_id;

                    //antes de atribuir o preço do item, verificar se a variant ta em dólar
                    //precisa realizar a cotação e multiplicar
                    if($item->variant && $item->variant->product){
                        // if($item->variant->product->currency == 'US$'){

                        //     if(isset($dolar_price['price'])){
                        //         $amount = $item->amount * $dolar_price['price'];
                        //     }else{
                        //         $amount = $item->amount * 1000;
                        //     }
                        // }else{
                        //     $amount = $item->amount;
                        // }

                        // if($discount){
                        //     $new_item->amount = $amount - ($amount * ($discount->percentage/100));
                        // }else{
                        //     $new_item->amount = $amount;
                        // }

                        // //caso seja um produto da s2m2, ainda tem q multiplicar por 1.05 para adicionar mais 5% de mão de obra no item
                        // if($item->variant->product->supplier->id == 56){
                        //     $new_item->amount = $new_item->amount * 1.05;
                        // }

                        $new_item->amount = 0;
                        $new_item->quantity = $item->quantity;

                        $new_item->save();

                        $total_amount += $new_item->amount;

                    }else{ //caso a variante tenha sido excluida
                        $variant = ProductVariants::withTrashed()->find($item->product_variant_id);

                        if($variant){
                            $product = Products::withTrashed()->find($variant->product_id);

                            if($product){
                                // if($product->currency == 'US$'){

                                //     if(isset($dolar_price['price'])){
                                //         $amount = $item->amount * $dolar_price['price'];
                                //     }else{
                                //         $amount = $item->amount * 1000;
                                //     }
                                // }else{
                                //     $amount = $item->amount;
                                // }

                                // if($discount){
                                //     $new_item->amount = $amount - ($amount * ($discount->percentage/100));
                                // }else{
                                //     $new_item->amount = $amount;
                                // }

                                // //caso seja um produto da s2m2, ainda tem q multiplicar por 1.05 para adicionar mais 5% de mão de obra no item
                                // if($product->supplier->id == 56){
                                //     $new_item->amount = $new_item->amount * 1.05;
                                // }

                                $new_item->amount = 0;

                                $new_item->quantity = $item->quantity;

                                $new_item->save();

                                $total_amount += $new_item->amount;
                            }
                        }
                    }

                }

                //$total_measurement - 100
                //$centimeters_supplier - X
                //$total * x = $cent_suplier * 100
                //x = $cent_suplier * 100 / $total

                $new_order->amount = $total_amount;
                $new_order->total_amount = $total_amount + $total_shipping;

                $new_order->save();
            }
        }

        $total_to_pay = SupplierOrders::where('order_id', $order->id)->sum('total_amount');

        return ['total_in_supplier_orders' => $total_to_pay, 'order_id' => $order->id];
    }

    public static function translateStatus($status){
	    switch ($status){
            case 'pending':
                return 'Pendente';
                break;
            case 'paid':
                return 'Pago';
                break;
            default:
                return 'Desconhecido';
                break;
        }

    }

    public function payGroup($group, $gateway, $payment_method){
        if($gateway == 'safe_to_pay'){
            return PaymentsService::payWithSafeToPay($group, $payment_method);
        }else{
            return ['status' => 'Error', 'message' => 'Erro inesperado, tente novamente.'];
        }
    }

    public static function sendReceiptToCustomer($order, $receipt){
        //Mail::to($order->customer->email)->send(new ReceiptMail($order, $receipt));
    }

    public static function paymentReceived($group){

        try {
            $group->status = 'paid';
            $group->save();

            $sup_orders = SupplierOrders::where('group_id', $group->id)->get();

            foreach($sup_orders as $s){
                //if($s->status != 'paid'){ //tinha um erro no upsell aqui, nesse passo precisa só verificar se a supplier order ainda não mudou pra paga, e muda-la caso seja necessário
                    $s->order->update(['status' => 'paid']);
                    $s->update(['status' => 'paid']);

                    //manda um email pra cada fornecedor que tem ordem no grupo
                    //Mail::to($s->supplier->email)->send(new NewOrderRequest($s->supplier->email));

                    //manda um email pro lojista falando que o pedido foi processado
                    //Mail::to($s->order->shop->email)->send(new ProcessedOrder($s->order));

                    //verifica, caso o frete da ordem tenha sido o Melhor Envio, faz a cotação de novo e compra o frete
                    //verifica também se já não salvou esse frete
                    $verifyFreteMelhorEnvio = FreteMelhorEnvio::where('order_id', $s->order->id)
                                                            ->where('supplier_id', $s->supplier->id)
                                                            ->where('supplier_order_id', $s->id)
                                                            ->first();

                    if($s->supplier->shipping_method == 'melhor_envio' && !$verifyFreteMelhorEnvio){
                        //como a ordem foi de fato paga, realiza a compra do frete na melhor envio (adiciona no carrinho)
                        $melhorEnvioService = new MelhorEnvioService();
                        $melhorEnvioService->setFromZipcode($s->supplier->address->zipcode);
                        $melhorEnvioService->setToZipcode($s->order->customer->address->zipcode);
                        $melhorEnvioService->prepareOrderProducts($s->order->items);
                        $responseMelhorEnvio = $melhorEnvioService->quoteBuyFreight($s->supplier, $s->order->shop, $s->order->customer, $s->order);
                        //dd($responseMelhorEnvio);
                        //salva o id vindo da melhor envio
                        if($s->supplier->shipping_method == 'melhor_envio' && $responseMelhorEnvio && $responseMelhorEnvio->freteId != ''){
                            $freteMelhorEnvio = FreteMelhorEnvio::firstOrCreate([
                                'order_id' => $s->order->id,
                                'supplier_id' => $s->supplier->id,
                                'supplier_order_id' => $s->id
                            ]);

                            $freteMelhorEnvio->amount = $responseMelhorEnvio->valor; //valor do frete
                            $freteMelhorEnvio->service_id = $responseMelhorEnvio->serviceId; //id to tipo de serviço 1 - PAC, 2 - SEDEX, 3 - Mini Envios
                            $freteMelhorEnvio->status = $responseMelhorEnvio->status;
                            $freteMelhorEnvio->melhor_envio_id = $responseMelhorEnvio->freteId; //id do frete adicionado ao carrinho da melhor envio
                            $freteMelhorEnvio->protocol = $responseMelhorEnvio->protocol; //salva o protocolo
                            $freteMelhorEnvio->save();
                        }

                        // legacy
                        // $freteMelhorEnvio = FreteMelhorEnvio::where('order_id', $s->order->id)
                        //                                     ->where('supplier_id', $s->supplier->id)
                        //                                     ->first();
                        // if($freteMelhorEnvio){
                        //     $freteMelhorEnvio->supplier_order_id = $s->id; //salva o id da SupplierOrder na instância da melhor envio

                        //     //realiza a compra do frete na melhor envio usando o saldo do fornecedor (gateway de pagamento será inserido no futuro)
                        //     $melhorEnvioService = new MelhorEnvioService($s->supplier->melhor_envio_settings);
                        //     $responseCart = $melhorEnvioService->payCartFreight($freteMelhorEnvio);

                        //     //dd($responseCart);

                        //     if($responseCart && $responseCart->tracking && $responseCart->status){
                        //         $freteMelhorEnvio->status = $responseCart->status;
                        //         $freteMelhorEnvio->tracking = $responseCart->tracking;
                        //         $freteMelhorEnvio->tag_url = $responseCart->tag_url;
                        //         $freteMelhorEnvio->save();

                        //         //já aproveita e atualiza o código de rastreio oficial
                        //         $s->shipping->tracking_number = $responseCart->tracking;
                        //         $s->shipping->company = $responseCart->company;
                        //         $s->shipping->tracking_url = $responseCart->link;
                        //         $s->shipping->save();
                        //     }
                        // }
                    }
                    //self::updateShopifyMawaDesc($s);
                //}
            }
        } catch (\Exception $e) {
            Log::error('paymentReceived error.', [$e]);
            report($e);
        }
    }

    public static function updateShopifyMawaDesc($supplier_order){
        //atualiza o pedido com o id gerado na mawa (assim como os Dsers)

        //primeiro carrega os dados da ordem e verifica senão tem o id de outro fornecedor la, que é o caso de uma ordem com produtos de fornecedores diferentes
        $client = new \GuzzleHttp\Client();

        $shop = $supplier_order->order->shop; // SupplierOrder
        $shopify_order_id = $supplier_order->order->external_id; //id na shopify

        $response = ShopifyService::GuzzleCalls($shop,'GET','orders/'.$shopify_order_id.'.json');

        $shopify_order = NULL;

        if($response->getStatusCode() == 200){
            $shopify_order = json_decode($response->getBody())->order;
        }

        if(!$shopify_order->fulfillment_status || $shopify_order->fulfillment_status != 'fulfilled'){ //caso não tenha sido processada ainda
            //atualiza a ordem na shopify com os ids da Mawa

            $note_attributes = $shopify_order->note_attributes ? $shopify_order->note_attributes : []; //pega os atributos anteriores caso existam

            //adiciona os novos atributos
            array_push($note_attributes, (object)[
                "name" => config('app.name')." ID".(count($note_attributes) > 0 ? '('.count($note_attributes).')' : ''),
                "value" => $supplier_order->f_display_id ]);

            $data = [
                'order' => (object)[
                    'id' => $shopify_order_id,
                    'note_attributes' => $note_attributes
                ]
            ];

            //salva os dados na shopify
            $response = ShopifyService::GuzzleCalls($shop,'PUT','orders/'.$shopify_order_id.'.json',false,false, $data);
       }
    }
}
