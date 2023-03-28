<?php

namespace App\Services\Shop;

use App\Exceptions\CustomException;

use App\Models\ErrorLogs;
use App\Models\OrderShippings;
use App\Models\ShopProducts;
use App\Models\Orders;
use App\Models\OrderItems;
use App\Models\OrderItemDiscounts;

use App\Models\Customers;
use App\Models\CustomerAddresses;

use App\Models\ProductVariants;
use App\Services\CurrencyService;
use App\Models\Suppliers;
use App\Models\FreteMelhorEnvio;
use App\Services\CorreiosService;
use App\Services\TotalExpressService;
use App\Services\MelhorEnvioService;
use App\Services\ChinaShippingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Models\ProductVariantStock;

use Automattic\WooCommerce\Client;


class WoocommerceService{

	public static function getPaidOrders($shop){
  
        try{
            $client = new \GuzzleHttp\Client([
                // Base URI is used with relative requests
                'base_uri' => $shop->woocommerce_app->domain,
            ]);
            $response = $client->request('GET', '/wp-json/wc/v3/orders?status=processing&limit=100', [
                'headers' => [
                    "Authorization" => "Basic ". base64_encode($shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue
            ]);
        
            if($response->getStatusCode() == 200){
                $orders = json_decode($response->getBody());
            }

            /*if($response->getHeaderLine('Link')){
                $orders = self::getAllOrders($shop, $orders, $response->getHeaderLine('Link'));
            }*/

            return ['status' => 'success', 'message' => 'Pedidos buscados no Woocommerce com sucesso.', 'data' => $orders];
        }catch(\Exception $e){

	        if($e->getCode() == 401){
                return ['status' => 'error', 'message' => 'Não conseguimos buscar seus pedidos no Woocommerce. Verifique se o seu APP PRIVADO no Woocommerce foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            }else{
	            report($e);
                ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            }
        }
	}

    public static function getPaidOrdersLimit($shop, $limit){
	    try{
            $client = new \GuzzleHttp\Client([
                // Base URI is used with relative requests
                'base_uri' => $shop->woocommerce_app->domain,
            ]);
            $response = $client->request('GET', '/wp-json/wc/v3/orders?status=processing&limit='.$limit, [
                'headers' => [
                    "Authorization" => "Basic ". base64_encode($shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue
            ]);
            if($response->getStatusCode() == 200){
                $orders = json_decode($response->getBody())->orders;
            }

            /*if($response->getHeaderLine('Link')){
                $orders = self::getAllOrders($shop, $orders, $response->getHeaderLine('Link'));
            }*/

            return ['status' => 'success', 'message' => 'Pedidos buscados no Woocommerce com sucesso.', 'data' => $orders];
        }catch(\Exception $e){
	        if($e->getCode() == 401){
                return ['status' => 'error', 'message' => 'Não conseguimos buscar seus pedidos no Woocommerce. Verifique se o seu APP PRIVADO no Woocommerce foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            }else{
	            report($e);
                ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            }
        }
	}

    public static function productsLimit ($shop){
	    try{
            $client = new \GuzzleHttp\Client([
                // Base URI is used with relative requests
                'base_uri' => $shop->woocommerce_app->domain,
            ]);
            $response = $client->request('GET', '/wp-json/wc/v3/products?limit=1', [
                'headers' => [
                    "Authorization" => "Basic ". base64_encode($shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue
            ]);
            if($response->getStatusCode() == 200){
                $productLimit = json_decode($response->getBody())->productLimit;
            }

            /*if($response->getHeaderLine('Link')){
                $orders = self::getAllOrders($shop, $orders, $response->getHeaderLine('Link'));
            }*/

            return ['status' => 'success', 'message' => 'Pedidos buscados no Woocommerce com sucesso.', 'data' => $productLimit];
        }catch(\Exception $e){
	        if($e->getCode() == 401){
                return ['status' => 'error', 'message' => 'Não conseguimos buscar seus pedidos no Woocommerce. Verifique se o seu APP PRIVADO no Woocommerce foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            }else{
	            report($e);
                ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            }
        }
	}

	public static function getAllOrders($shop, $orders, $link_header){
        ErrorLogs::create(['status' => 0, 'message' => $link_header, 'file' => '0']);

        $url = parse_url($link_header);

        parse_str($url['query'], $params);

        $pageInfo = substr($params['page'], 0, strpos($params['page'], ">;"));

        if($pageInfo){
            $client = new \GuzzleHttp\Client([
                // Base URI is used with relative requests
                'base_uri' => $shop->woocommerce_app->domain,
            ]);
            $response = $client->request('GET', '/wp-json/wc/v3/orders?page='.$pageInfo.'&limit=50', [
                'headers' => [
                    "Authorization" => "Basic ". base64_encode($shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue
            ]);

            if($response->getStatusCode() == 200){
                $orders = array_merge($orders, json_decode($response->getBody())->orders);

                if($response->getHeaderLine('Link')){
                    $orders = self::getAllOrders($shop, $orders, $response->getHeaderLine('Link'));
                }
            }
        }

        return $orders;
    }

	public static function registerOrder($shop, $woocommerce_order){
        try {
            if($shop->status == 'inactive'){
                return false;
            }
            //dd($woocommerce_order);
            if(!isset($woocommerce_order->customer_id) || !$woocommerce_order->customer_id){
                return false;
            }
            
            if(!self::checkOrderItems($woocommerce_order->line_items)){
                return false;
            }

            $order = Orders::firstOrNew(['shop_id' => $shop->id, 'external_id' => $woocommerce_order->id]);

            if($order->id != null){
                return false;
            }

            $order->external_service = 'woocommerce';
            $order->name = "#".$woocommerce_order->number;
            $order->email = $woocommerce_order->billing->email;
            $order->external_price = $woocommerce_order->total;
            $order->external_usd_price = $woocommerce_order->total/5.34;
            $order->landing_site = $woocommerce_order->_links->self[0]->href;
            $order->status = 'pending';
            $order->external_created_at = date('Y-m-d h:i:s', strtotime($woocommerce_order->date_created_gmt));

            if(!$order->save()){
                return null;
            }

            $items = self::registerItems($order, $woocommerce_order->line_items);
            $customer = self::registerCustomer($shop, $woocommerce_order, $woocommerce_order->billing->address_1);
            $shipping = self::registerShipping($order, $items['items'], $customer, $shop);            

            if(!$customer || !$items || !$shipping || $items['items'][0]['quantity'] == 0){
                $order->external_id = null;
                $order->save();
                $order->delete();

                return null;
            }

            $order->customer_id = $customer->id;
            $order->items_amount = $items['items'][0]['quantity'];
            $order->shipping_amount = $shipping['total_shipping_amount'];
            $order->amount = $items['items'][0]['quantity'] + $shipping['total_shipping_amount'];
            $order->save();

            return $order;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
	}

	/* Verify if theres a registered product in the woocommerce order */
	public static function checkOrderItems($woocommerce_line_items){

        try {
            foreach ($woocommerce_line_items as $woocommerce_item) {
                //caso seja um kit, retira os dados kit-unidades antes de fazer essa verificação
                $arrSkuCheck = explode("-", $woocommerce_item->sku);

                if(count($arrSkuCheck) > 2 && $arrSkuCheck[1]){ //caso tenha a segunda posição no vetor
                    $unidadesSkuCheck = intval($arrSkuCheck[1]);
                }else{
                    $unidadesSkuCheck = 0;
                }

                if(strtoupper($arrSkuCheck[0]) == "KIT" && $unidadesSkuCheck > 0){
                    $stringSku = "";
                    for($i = 2; $i < count($arrSkuCheck); $i++){
                        $stringSku.=$arrSkuCheck[$i].($i < count($arrSkuCheck) - 1 ? "-" : ""); //monta a string novamente
                    }

                    $variant = ProductVariants::where('sku', $stringSku)->first();
                }else{
                    $variant = ProductVariants::where('sku', $woocommerce_item->sku)->first();
                }
                

                if($variant){
                    return true;
                }
            }

            return false;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
	}

	public static function registerItems($order, $woocommerce_line_items){

        try {
            $items = array();
            $total_amount = 0;
            
            foreach ($woocommerce_line_items as $woocommerce_item) {
                //antes, verifica se é um kit, caso seja, multiplica pela quantidade que indica na string
                $arrSku = explode("-", $woocommerce_item->sku);

                //a string deve ter o padrão kit-unidades-skuitem
                if(count($arrSku) > 2 && $arrSku[1]){ //caso tenha a segunda posição no vetor
                    $unidadesSku = intval($arrSku[1]);
                }else{
                    $unidadesSku = 0;
                }

                if(strtoupper($arrSku[0]) == "KIT" && $unidadesSku > 0){
                    $stringSku = "";
                    for($i = 2; $i < count($arrSku); $i++){
                        $stringSku.=$arrSku[$i].($i < count($arrSku) - 1 ? "-" : ""); //monta a string novamente
                    }

                    $variant = ProductVariants::where('sku', $stringSku)->first();

                    $aplicatedDiscount = 0; //desconto aplicado
                    $orderItemDiscount = NULL; //salva o desconto usado no item (caso exista)

                    //caso tenha o token do bling e caso não aceite pedidos com estoque zerado, sai fora
                    if($variant->product->supplier->bling_apikey && $variant->product->supplier->empty_stock_bling == 'nao'){
                        continue;
                    }

                    if($variant){
                        $variant_id = $variant->id;
                        $charge_or_not = 1;

                        //antes de fazer a soma desse valor, verifica se o produto fornece desconto por quantidade
                        //caso ofereça, calcula qual é o desconto e salva, depois soma o valor subtraindo o desconto (porcentagem sobre a quantidade)
                        // if(isset($variant->product->discounts) && count($variant->product->discounts) > 0){ //caso possua algum desconto
                        //     $indiceDiscount = 0;
                        //     $minDistance = 9999999; //distancia entre a quantidade e algum desconto oferecido
                        //     $distance = 9999999;
                        //     $orderItemDiscount = new OrderItemDiscounts();

                        //     foreach ($variant->product->discounts as $key => $discount) {
                        //         $distance = ($woocommerce->quantity * $unidadesSku) - $discount->quantity;
                        //         if($distance >= 0 && $distance < $minDistance){
                        //             $indiceDiscount = $key;
                        //             $minDistance = $distance;
                        //             $aplicatedDiscount = $discount->value; //valor do desconto à ser aplicado
                        //             $orderItemDiscount->product_discount_id = $discount->id;
                        //         }
                        //     }
                        //     //caso a distância seja 0, quer dizer q não há descontos
                        //     if($distance < 0 && $minDistance == 9999999){
                        //         $orderItemDiscount = NULL;
                        //     }
                        // }

                        $amount = ($variant->price * $woocommerce_item->quantity * $unidadesSku); //salva x vezes a quantidade de items do kit
                        $amount = $amount - ($amount * ($aplicatedDiscount/100));

                        $total_amount += $amount;
                    }else{
                        continue;
                    }

                    $item = new OrderItems();
                    $item->order_id = $order->id;
                    $item->product_variant_id = $variant_id;
                    $item->external_service = 'woocommerce';
                    $item->external_product_id = $woocommerce_item->product_id;
                    $item->external_variant_id = $woocommerce_item->variation_id;
                    $item->sku = $stringSku; //sku sem a tag
                    $item->title = $woocommerce_item->name;
                    $item->quantity = $woocommerce_item->quantity * $unidadesSku;
                    $item->amount = $amount;
                    $item->external_price = $woocommerce_item->price;
                    $item->charge = $charge_or_not;

                    $item->save();

                    if($orderItemDiscount){ //caso haja desconto, salva o item o OrderItem
                        $orderItemDiscount->order_item_id = $item->id;
                        $orderItemDiscount->save();
                    }

                    array_push($items, $item);
                }else{ //caso não seja um kit faz com o sku normal

                    $variant = ProductVariants::where('sku', $woocommerce_item->sku)->first();

                    $aplicatedDiscount = 0; //desconto aplicado
                    $orderItemDiscount = NULL; //salva o desconto usado no item (caso exista)

                    if($variant){
                        $variant_id = $variant->id;
                        $charge_or_not = 1;

                        //antes de fazer a soma desse valor, verifica se o produto fornece desconto por quantidade
                        //caso ofereça, calcula qual é o desconto e salva, depois soma o valor subtraindo o desconto (porcentagem sobre a quantidade)
                        // if(isset($variant->product->discounts) && count($variant->product->discounts) > 0){ //caso possua algum desconto
                        //     $indiceDiscount = 0;
                        //     $minDistance = 9999999; //distancia entre a quantidade e algum desconto oferecido
                        //     $distance = 9999999;
                        //     $orderItemDiscount = new OrderItemDiscounts();

                        //     foreach ($variant->product->discounts as $key => $discount) {
                        //         $distance = $shopify_item->quantity - $discount->quantity;
                        //         if($distance >= 0 && $distance < $minDistance){
                        //             $indiceDiscount = $key;
                        //             $minDistance = $distance;
                        //             $aplicatedDiscount = $discount->value; //valor do desconto à ser aplicado
                        //             $orderItemDiscount->product_discount_id = $discount->id;
                        //         }
                        //     }
                        //     //caso a distância seja 0, quer dizer q não há descontos
                        //     if($distance < 0 && $minDistance == 9999999){
                        //         $orderItemDiscount = NULL;
                        //     }
                        // }
                                                
                        $amount = ($variant->price * $woocommerce_item->quantity);
                        $amount = $amount - ($amount * ($aplicatedDiscount/100));
                        $total_amount += $amount;
                    }else{
                        continue;
                    }
                    
                    $item = new OrderItems();

                    $item->order_id = $order->id;
                    $item->product_variant_id = $variant_id;
                    $item->external_service = 'woocommerce';
                    $item->external_product_id = $woocommerce_item->product_id;
                    $item->external_variant_id = $woocommerce_item->variation_id;
                    $item->sku = $woocommerce_item->sku;
                    $item->title = $woocommerce_item->name;
                    $item->quantity = $woocommerce_item->quantity;
                    $item->amount = $amount;
                    $item->external_price = $woocommerce_item->price;
                    $item->charge = $charge_or_not;

                    $item->save();
                    
                    if($orderItemDiscount){ //caso haja desconto, salva o item o OrderItem
                        $orderItemDiscount->order_item_id = $item->id;
                        $orderItemDiscount->save();
                    }                  

                    array_push($items, $item);


                }                
            }
            return ['items' => $items, 'total_amount' => $total_amount];
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
	}

	public static function registerShipping($order, $items, $customer, $shop){

        try {
            $shipping_items = [];
            $total_shipping_amount = 0;

            foreach($items as $item){
                if($item->variant && $item->variant->product){
                    $shipping_items[$item->variant->product->supplier_id][] = $item;
                }
            }

            $shippings = [];
            foreach($shipping_items as $supplier_id => $items){
                $supplier = Suppliers::find($supplier_id);

                if($supplier){
                    
                    $shipping_amount = self::calculateShipping($items, $supplier, $customer);

                    if($supplier->shipping_fixed_fee && $supplier->shipping_fixed_fee > 0){
                        $shipping_amount += $supplier->shipping_fixed_fee;
                    }

                    $shipping = new OrderShippings();

                    $shipping->supplier_id = $supplier->id;
                    $shipping->order_id = $order->id;                    

                    if($supplier->id == 56){ //caso seja a s2m2 adiciona os 4% de taxa no frete do produto também
                        $shipping_amount = $shipping_amount * 1.05;
                    }

                    if($supplier->id == 43){ //caso seja a ksimports adiciona os R$ 5,00 de taxa no frete
                        $shipping_amount = $shipping_amount + 5.00;
                    }

                    $shipping->amount = $shipping_amount;

                    $shipping->save();

                    $shippings[] = $shipping;                    

                    $total_shipping_amount += $shipping_amount;
                }
            }

            return ['total_shipping_amount' => $total_shipping_amount, 'shippings' => $shippings];
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    //por conta da melhor envios agora tbm é necessário os dados do lojista
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

	public static function registerCustomer($shop, $woocommerce_customer, $woocommerce_address){

        try {
            $customer = Customers::firstOrCreate(['shop_id' => $shop->id, 'external_id' => $woocommerce_customer->id]);

            $customer->external_service = 'woocommerce';
            $customer->first_name = $woocommerce_customer->billing->first_name;
            $customer->last_name = $woocommerce_customer->billing->last_name;
            $customer->email = $woocommerce_customer->billing->email;
            $customer->total_spent = $woocommerce_customer->total_tax;

            if(!$customer->save()){
                return null;
            }

            $address = self::registerCustomerAddress($customer, $woocommerce_customer);

            if(!$address){
                return null;
            }

            return $customer;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
	}

	public static function registerCustomerAddress($customer, $woocommerce_customer){

        try {
            $address = CustomerAddresses::firstOrCreate(['customer_id' => $customer->id, 'address1' => $woocommerce_customer->billing->address_1]);

            $address->name = $woocommerce_customer->billing->first_name." ".$woocommerce_customer->billing->last_name;
            $address->company = $woocommerce_customer->billing->company;
            $address->address1 = $woocommerce_customer->billing->address_1;
            $address->city = $woocommerce_customer->billing->city;
            $address->province = $woocommerce_customer->billing->state;
            $address->country = $woocommerce_customer->billing->country;
            $address->zipcode = $woocommerce_customer->billing->postcode;
            $address->phone = $woocommerce_customer->billing->phone;
            $address->province_code = 1;
            $address->country_code = 55;

            if(!$address->save()){
                $customer->delete();
                return null;
            }

            return $address;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
	}

	public static function registerProduct($shop, $product){
        try {

            $woocommerce = new Client(
                $shop->woocommerce_app->domain, 
                $shop->woocommerce_app->app_key, 
                $shop->woocommerce_app->app_password,
                [
                    'version' => 'wc/v3',
                    'timeout' => 400
                ]
            );
            //dd($woocommerce->get('products/917'));
            //cria as opções do atributo
            $terms = []; //opções do atributo
            // foreach ($product->variants as $variant) {
                
            // }          

            if($shop->status == 'inactive'){
                return false;
            }

            $newAttr[0] = (object)[
                'name' => 'Tipo',
                'visible' => true,
                'variation' => true,
                'options' => $terms
            ];


            $consultproduto = ProductVariants::where('product_id', $product->id)->first();
            $stock = ProductVariantStock::where('product_variant_id', $consultproduto->id)->first(); 
            $data = (object)[
                    'name' => $product->title,
                    'description' => $product->description,
                    'type' => 'simple',
                    'regular_price' => '0.00',
                    'weight' => $product->variants[0]->weight,
                    'images' => [],
                    'attributes' => $newAttr,
                    'sku' => '',
                    'manage_stock' => true,
                    'stock_quantity' => $stock->quantity,
            ];            

            if($product->img_source){
                if(substr($product->img_source, -4) != 'webp'){
                    if(@getimagesize($product->img_source)){
                        $data->images[] = (object)['src' => $product->img_source];
                    }                    
                }                
            }
            
            foreach ($product->images as $image) {
                if(substr($image->src, -4) != 'webp'){
                    if(@getimagesize($image->src)){ 
                        $data->images[] = (object)['src' => $image->src];
                    }                    
                }                
            }            

            foreach ($product->variants as $variant) {
                if(count($product->variants) > 1){
                    $data->type = 'variable';
                }else{ //caso contrário é um produto simples
                    $data->type = 'simple';
                    $data->sku = $variant->sku;
                }
                
                //cria termos
                //$termsData['create'][] = [ 'name' => $variant->title ];
                $terms[] = str_replace(" ", "-", $variant->title);

                //cria variantes
                $variantsData['create'][] = [
                    'regular_price' => $variant->price,
                    'sku' => $variant->sku,
                    'attributes' => [
                        [
                            'name' => 'Tipo',
                            'option' => str_replace(" ", "-", $variant->title)
                        ]
                    ],
                    'url_image' => $variant->img_source,
                    'image' => [
                        'id' => null
                    ]
                ];
                
                if($variant->img_source){
                    //retira imagens em .webp
                    if(substr($variant->img_source, -4) != 'webp'){
                        if(@getimagesize($variant->img_source)){
                            $data->images[] = (object)['src' => $variant->img_source];
                        }                        
                    }
                }
            }
            
            $w_product = $woocommerce->post('products', $data); //adiciona o produto
            
            //antes, pega os ids das imagens de cada variante (OTIMIZAR ISSO AQUI DPS)
            //dd($w_product);
            foreach($variantsData['create'] as $key => $variantData){
                foreach($w_product->images as $imageProduct){
                    $infosFile = pathinfo($variantData['url_image']); //extrai as informações do link
                    if(strpos($imageProduct->src, $infosFile['filename'])){ //verifica se o nome é uma substring do nome que foi adicionado no woocommerce
                        $variantsData['create'][$key]['image'] = [ 'id' => $imageProduct->id ];
                        continue;
                    }
                }
            }
            
            //dd($variantsData);            
            $w_variant = $woocommerce->post('products/'.$w_product->id.'/variations/batch', $variantsData); //adiciona as variantes

            return $w_product;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

            return false;
        }
	}

	public static function updateOrderShipping($supplier_order, $woocommerce_order_id, $shipping){
        $shop = $supplier_order->order->shop;

        $client = new \GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => $shop->woocommerce_app->domain,
        ]);
        $response = $client->request('GET', '/wp-json/wc/v3/orders/'.$woocommerce_order_id, [
            'headers' => [
                "Authorization" => "Basic ". base64_encode($shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password)
            ],
            'verify' => false, //only needed if you are facing SSL certificate issue
        ]);
        
        if($response->getStatusCode() == 200){
            $woocommerce_order = json_decode($response->getBody());
        }        
        
        try {
            //faz requisição para adicionar o rastreio pelo plugin
            $response = $client->request('post', '/wp-json/wc-ast/v3/orders/'.$woocommerce_order_id.'/shipment-trackings', [
                'headers' => [
                    "Authorization" => "Basic ". base64_encode($shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue,
                'form_params' => [
                    "tracking_provider" => $shipping->company,
                    "tracking_number" => $shipping->tracking_number,
                    "tracking_link" => $shipping->tracking_url,
                    "status_shipped" => 1, //status_shipped is optional parameter
                    "replace_tracking" => 1 //replace_tracking is optional parameter
                ]
            ]);
            
            if($response->getStatusCode() == 201){
                $fulfillment = json_decode($response->getBody())->tracking_id;

                $supplier_order->shipping->external_service = 'woocommerce';
                $supplier_order->shipping->external_fulfillment_id = $fulfillment; //não é o fullfilment id, mas serve por enquanto
                $supplier_order->shipping->save();

                return true;
            }else{
                return false;
            }
        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

            return false;
        }
	}

	// public static function updateFulfillment($supplier_order, $shipping){
	// 	$shop = $supplier_order->order->shop;

	// 	try {
	// 		$client = new \GuzzleHttp\Client();

	// 		$data = [
	// 			'fulfillment' => (object)[
	// 				'notify_customer' => true,
	// 				'tracking_info' => (object)[
	// 					'number' => $shipping->tracking_number,
	// 					'url' => $shipping->tracking_url,
	// 					'company' => $shipping->company,
	// 				]
	// 			]
	// 		];

	// 		$response = $client->request('POST', 'https://'.$shop->shopify_app->app_key.':'.$shop->shopify_app->app_password.'@'.$shop->shopify_app->domain.'.myshopify.com/admin/api/2020-04/fulfillments/'.$shipping->external_fulfillment_id.'/update_tracking.json');

	// 		if($response->getStatusCode() == 200){
	// 			return true;
	// 		}else{
	// 			return false;
	// 		}
	// 	} catch (\Exception $e) {
	// 	    report($e);
    //         ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

	// 		return false;
	// 	}

	// }

	// public static function cancelFulfillment($supplier_order, $shipping){
	// 	$shop = $supplier_order->order->shop;

	// 	try {
	// 		$client = new \GuzzleHttp\Client();
	// 		$response = $client->request('POST', 'https://'.$shop->shopify_app->app_key.':'.$shop->shopify_app->app_password.'@'.$shop->shopify_app->domain.'.myshopify.com/admin/api/2020-04/fulfillments/'.$shipping->external_fulfillment_id.'/cancel.json');

	// 		if($response->getStatusCode() == 200){
	// 			return true;
	// 		}else{
	// 			return false;
	// 		}
	// 	} catch (\Exception $e) {
	// 	    report($e);
    //         ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

    //         return false;
	// 	}
	// }

    public static function registerProductJson($shop, $product){
        //registra um produto na woocommerce e retorna o id em caso de sucesso ou error em caso de falha
        try {
            //carrega os dados do shop e do produto passados
            
            if($shop->status == 'inactive'){
                return false;
            }

            $data = (object)[
                'product' => (object)[
                    'title' => $product->title,
                    'body_html' => $product->description,
                    'vendor' => $product->supplier->name,
                    'published' => false,
                    'options' => [],
                    'variants' => [],
                    'images' => [

                    ]
                ]
            ];

            if($product->img_source){
                $data->product->images[] = (object)['src' => $product->img_source];
            }
            
            foreach ($product->images as $image) {
                $data->product->images[] = (object)['src' => $image->src];
            }
            
            
            foreach ($product->options as $option) {
                $values = [];

                foreach ($product->variants as $variant) {
                    foreach ($variant->options_values->where('product_option_id', $option->id) as $option_value) {
                        $values[] = $option_value->value;
                    }
                }

                $data->product->options[] = (object)[
                    'name' => $option->name
                ];
            }
            
            
            foreach ($product->variants as $variant) {
                $i = 1;
                $variant_data = [];

                foreach ($variant->options_values as $option_value) {
                    $variant_data["option".$i] = $option_value->value;

                    $i++;
                }

                $variant_data['price'] = '0.00';
                $variant_data['sku'] =  $variant->sku;
                $variant_data['weight'] = ($variant->weight_in_grams != null) ? $variant->weight_in_grams : 0;
                $variant_data['weight_unit'] = 'g';
                $variant_data['fulfillment_service'] = 'manual';

                $data->product->variants[] = (object)$variant_data;

                if($variant->img_source){
                    $data->product->images[] = (object)['src' => $variant->img_source];
                }
            }

            $client = new \GuzzleHttp\Client([
                // Base URI is used with relative requests
                'base_uri' => $shop->woocommerce_app->domain,
            ]);
            
            $response = $client->request('post', '/wp-json/wc/v3/products', [
                'headers' => [
                    "Authorization" => "Basic ". base64_encode($shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue
                'json' => [$data,]
                ]);
            
            //dd(json_decode($response->getBody())->product);
            if($response->getStatusCode() == 200 || $response->getStatusCode() == 201){
                return json_decode($response->getBody())->product;
            }
            return false;
        } catch (\Exception $th) {
            return false;
        }        
    }

    public static function registerImagesProductJson($shop, $woocommerce_product, $product){
        try {
            // Upload images
            $woocommerce_product = (object)$woocommerce_product;
            
            $client = new \GuzzleHttp\Client();

            foreach ($woocommerce_product->variants as $variant) {
                $variant = (object)$variant;
                $local_variant = $product->variants->where('sku', $variant->sku)->first();

                if($local_variant->img_source){
                    $data = [
                        'image' => (object)[
                            'src' => $local_variant->img_source,
                            'variant_ids' => [
                                $variant->id
                            ]
                        ]
                    ];

                    $response = $client->request('post', '/wp-json/wc/v3/products/'.$woocommerce_product->id.'/images', [
                        'headers' => [
                            "Authorization" => "Basic ". base64_encode($shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password)
                        ],
                        'verify' => false, //only needed if you are facing SSL certificate issue
                        'json' => [$data,]
                        ]);

                    // $response = $client->request('POST', 'https://'.$shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password.'@'.$shop->shopify_app->domain.'.myshopify.com/admin/api/2021-04/products/'.$shopify_product->id.'/images.json');
                }
            }
            ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['woocommerce_product_id' => $woocommerce_product->id]);

            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }        
    }
}
