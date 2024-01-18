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
use App\Models\ProductVariantStock;
use App\Services\CurrencyService;
use App\Models\Suppliers;
use App\Services\CorreiosService;
use App\Services\TotalExpressService;
use App\Services\ChinaShippingService;
use Illuminate\Support\Facades\Log;
use App\Services\MelhorEnvioService;

class CartxService{

    public static function getPaidOrders($shop){
	    try{
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://accounts.cartx.io/api/'.$shop->cartx_app->domain.'/orders?created_at_min&created_at_max&updated_at_min&updated_at_max&payment_status=3',
                                            ['headers' => [
                                                'Authorization' => 'Bearer '.$shop->cartx_app->token,
                                                'Accept' => 'application/json',
                                            ]
                        ]);

            $orders = json_decode($response->getBody())->orders->data;

            /*if($response->getHeaderLine('Link')){
                $orders = self::getAllOrders($shop, $orders, $response->getHeaderLine('Link'));
            }*/

            return ['status' => 'success', 'message' => 'Pedidos buscados no cartx com sucesso.', 'data' => $orders];
        }catch(\Exception $e){
	        if($e->getCode() == 401){
                return ['status' => 'error', 'message' => 'Não conseguimos buscar seus pedidos no cartx. Verifique se o seu Token no cartx foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            }else{
	            report($e);
                ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            }
        }
    }
    
    public static function registerOrder($shop, $cartx_order){
        
        try {
            if($cartx_order->status_id == "Open" || $cartx_order->status_id == "New"){ //caso não seja uma ordem que tenha sido cancelada
                if($shop->status == 'inactive'){
                    return false;
                }
    
                if(!isset($cartx_order->customer) || !$cartx_order->customer){
                    return false;
                }
    
                //pega o sku dos itens
                
                $cartx_order->line_items = self::getSkuLineItems($shop, $cartx_order->line_items);
                
                if(!self::checkOrderItems($cartx_order->line_items)){
                    return true;
                }            
    
                $order = Orders::firstOrNew(['shop_id' => $shop->id, 'external_id' => $cartx_order->id]);
                
                if($order->id != null){
                    return true;
                }
    
                $order->external_service = 'cartx';
                $order->name = $cartx_order->name;
                $order->email = $cartx_order->email;
                $order->external_price = $cartx_order->total_price;
                //$order->external_usd_price = $cartx_order->total_price_usd; (não nulo, mudei manualmente no bd local)
                $order->landing_site = $cartx_order->landing_site;
                $order->status = 'pending';
                $order->external_created_at = date('Y-m-d h:i:s', strtotime($cartx_order->created_at));
    
                if(!$order->save()){
                    return null;
                }
    
                $items = self::registerItems($order, $cartx_order->line_items);
    
                $cartx_order->customer->total_spent = $cartx_order->total_price; //nao vem esse campo no customer pela api do cartx
    
                $customer = self::registerCustomer($shop, $cartx_order->customer, $cartx_order->address);
                $shipping = self::registerShipping($order, $items['items'], $customer);
                
                if(!$customer || !$items || !$shipping || $items['total_amount'] == 0){
                    $order->external_id = null;
                    $order->save();
                    $order->delete();
    
                    return null;
                }
    
                $order->customer_id = $customer->id;
                $order->items_amount = $items['total_amount'];
                $order->shipping_amount = $shipping['total_shipping_amount'];
                $order->amount = $items['total_amount'] + $shipping['total_shipping_amount'];
                $order->save();
    
                return $order;
            }
            
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    //função utilizada para pegar o sku dos itens na cartx
    private static function getSkuLineItems($shop, $cartx_line_items){
        $newLineItems = $cartx_line_items;
        
        try {
            foreach ($newLineItems as $key => $cartx_item) {
                //precisa fazer uma nova requisição para pegar o sku dos itens no cartx
                $client = new \GuzzleHttp\Client();
                
                $response = $client->request('GET', 'https://accounts.cartx.io/api/'.$shop->cartx_app->domain.'/products/'.$cartx_item->product_id,
                                                ['headers' => [
                                                    'Authorization' => 'Bearer '.$shop->cartx_app->token,
                                                    'Accept' => 'application/json',
                                                ]
                            ]);

                $productsVariants = json_decode($response->getBody())->product->product_variants;

                //busca o id do product variant
                foreach ($productsVariants as $productVariant) {
                    if($productVariant->id == $cartx_item->variant_id){
                        
                        $variant = ProductVariants::where('sku', $productVariant->sku)->first();
                        
                        if($variant){
                            $newLineItems[$key]->sku = $productVariant->sku; //atribui o sku desse produto como sendo o sku da variação dele escolhida                            
                            continue; //caso tenha encontrado pula para o próximo item
                        }
                    }
                }
            }

            return $newLineItems;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }
    
    /* Verify if theres a registered product in the shopify order */
	public static function checkOrderItems($cartx_line_items){
        try {
            foreach ($cartx_line_items as $cartx_item) {
                $variant = ProductVariants::where('sku', $cartx_item->sku)->first();

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
    
    public static function registerItems($order, $cartx_line_items){
        try {
            $items = [];
            $total_amount = 0;

            foreach ($cartx_line_items as $cartx_item) {
                $item = new OrderItems();

                $variant = ProductVariants::where('sku', $cartx_item->sku)->first();

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

                    //agora só multiplica o preço pela quantidade, pq quando for em dólar, o valor será convertido na hora que gerar a fatura
                    $amount = ($variant->price * $cartx_item->quantity);
                    $amount = $amount - ($amount * ($aplicatedDiscount/100));

                    $total_amount += $amount;
                }else{
                    continue;
                }

                $item->order_id = $order->id;
                $item->product_variant_id = $variant_id;
                $item->external_service = 'cartx';
                $item->external_product_id = $cartx_item->product_id;
                $item->external_variant_id = $cartx_item->variant_id;
                $item->sku = $cartx_item->sku;
                $item->title = $cartx_item->title;
                $item->quantity = $cartx_item->quantity;
                $item->amount = $amount;
                $item->external_price = $cartx_item->price;
                $item->charge = $charge_or_not;

                $item->save();

                if($orderItemDiscount){ //caso haja desconto, salva o item o OrderItem
                    $orderItemDiscount->order_item_id = $item->id;
                    $orderItemDiscount->save();
                }

                $items[] = $item;
            }

            return ['items' => $items, 'total_amount' => $total_amount];
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }
    
    public static function registerCustomer($shop, $cartx_customer, $cartx_address){
        try {
            $customer = Customers::firstOrCreate(['shop_id' => $shop->id, 'external_id' => $cartx_customer->id]);

            $customer->external_service = 'cartx';
            $customer->first_name = $cartx_customer->first_name;
            $customer->last_name = $cartx_customer->last_name;
            $customer->email = $cartx_customer->email;
            $customer->total_spent = $cartx_customer->total_spent;

            if(!$customer->save()){
                return null;
            }

            $address = self::registerCustomerAddress($customer, $cartx_address);

            if(!$address){
                return null;
            }

            return $customer;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
	}

	public static function registerCustomerAddress($customer, $cartx_address){
        try {
            $address = CustomerAddresses::firstOrCreate(['customer_id' => $customer->id, 'address1' => $cartx_address->address1]);

            $address->name = $cartx_address->name;
            $address->company = $cartx_address->company;
            $address->address2 = $cartx_address->address2;
            $address->city = $cartx_address->city;
            $address->province = $cartx_address->province;
            $address->country = $cartx_address->country;
            $address->zipcode = $cartx_address->zip;
            $address->phone = $cartx_address->phone;
            $address->province_code = $cartx_address->province_code;
            $address->country_code = $cartx_address->country_code;

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
    
    public static function registerShipping($order, $items, $customer){
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

                    if($supplier->id == 56){ //caso seja a s2m2 adiciona os 5% de taxa no frete do produto também
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

    public static function calculateShipping($items, $supplier, $customer){
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

	public static function registerProduct($shop, $product){
        try {
            if($shop->status == 'inactive'){
                return false;
            }

            //cria um objeto no formato da cartx para enviar na requisição
            $data = (object)[
                'product' => (object)[
                    'title' => $product->title,
                    'body_html' => $product->description,
                    'vendor' => $product->supplier->name,
                    'tags' => '', //tags relacionadas ao produto, obrigatória (pode ser uma string vazia)
                    'options' => [],
                    'variants' => [],
                    'images' => []
                ]
            ];

            // if($product->img_source){
            //     $data->product->images[] = (object)['src' => $product->img_source];
            // }

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
                    'name' => $option->name,
                    'values' => [$option->name]
                ];
            }

            foreach ($product->variants as $variant) {
                $i = 1;
                $variant_data = [];

                //campos obrigatórios do cartx
                $variant_data["option1"] = null;
                $variant_data["option2"] = null;
                $variant_data["option3"] = null;

                if($variant->options_values){ //caso o produto tenha opções
                    foreach ($variant->options_values as $option_value) {
                        $variant_data["option".$i] = $option_value->value;    
                        $i++;
                    }
                }     
                
                
                $consultproduto = ProductVariants::where('sku', $variant->sku)->first();
                $stock = ProductVariantStock::where('product_variant_id', $consultproduto->id)->first(); 
                $variant_data["inventory_quantity"] = $stock->quantity;
                

                $variant_data['price'] = $variant->price;
                $variant_data['sku'] =  $variant->sku;
                $variant_data['weight'] = ($variant->weight_in_grams != null) ? $variant->weight_in_grams : 0;
                $variant_data['weight_unit'] = 'g';
                $variant_data['fulfillment_service'] = 'manual';

                $data->product->variants[] = (object)$variant_data;

                if($variant->img_source){
                    $data->product->images[] = (object)['src' => $variant->img_source];
                }
            }
            
            $client = new \GuzzleHttp\Client();
            
            $response = $client->request('POST',
                    'https://accounts.cartx.io/api/'.$shop->cartx_app->domain.'/import-nimble-product',
                    [
                        'json' => $data,
                        'headers' => [
                            'Authorization' => 'Bearer '.$shop->cartx_app->token,
                            'Accept' => 'application/json',
                        ]
                    ]);
                    
            $cartx_product = json_decode($response->getBody())->product;
            
            //caso dê certo atualiza o produto com o id vindo do cartx
            ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['cartx_product_id' => $cartx_product->id]);

            return $cartx_product;
        } catch(\Throwable $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

            return false;
        }
    }
    
    public static function updateOrderShipping($supplier_order, $cartx_order_id, $shipping){
        $shop = $supplier_order->order->shop;

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://accounts.cartx.io/api/'.$shop->cartx_app->domain.'/orders/'.$cartx_order_id,
                                        [
                                            'headers' => [
                                                'Authorization' => 'Bearer '.$shop->cartx_app->token,
                                                'Accept' => 'application/json',
                                            ]
                                        ]);

        if($response->getStatusCode() == 200){
            $cartx_order = json_decode($response->getBody())->order;
        }

        if($cartx_order->fulfillment_status == 'Fully Fulfilled'){
            return true;
        }

        $line_items = [];

        try {
            foreach ($cartx_order->line_items as $item) {
                $order_item = $supplier_order->order->items->where('external_variant_id', $item->variant_id)->first();

                if($order_item){
                    if(in_array($order_item->product_variant_id, $supplier_order->items->pluck('product_variant_id')->toArray())){
                        $line_items[] = (object)['id' => $item->id];
                    }
                }
            }

            $data = [
                'fulfillment' => (object)[
                    'notify_customer' => 1,  
                    'tracking_number' => $shipping->tracking_number,
                    'line_items' => $line_items
                ]
            ];

            $response = $client->request('POST', 'https://accounts.cartx.io/api/'.$shop->cartx_app->domain.'/orders/'.$cartx_order_id.'/fulfill', [
                                                'json' => $data,
                                                'headers' => [
                                                    'Authorization' => 'Bearer '.$shop->cartx_app->token,
                                                    'Accept' => 'application/json',
                                                ]]);
            
            

            if($response->getStatusCode() == 200){

                $fulfillment = json_decode($response->getBody())->order->fulfillments[0];

                $supplier_order->shipping->external_service = 'cartx';
                $supplier_order->shipping->external_fulfillment_id = $fulfillment->id;
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

	public static function updateFulfillment($supplier_order, $shipping){
        
        $shop = $supplier_order->order->shop;

		try {
			$client = new \GuzzleHttp\Client();

			$data = [
				'fulfillment' => (object)[
                    'id' => $shipping->external_fulfillment_id,
					'notify_customer' => 1,
					'tracking_company' => $shipping->company,
                    'tracking_numbers' => [
                        $shipping->tracking_number
                    ],
                    'tracking_urls' => [
                        $shipping->tracking_url
                    ],
				]
            ];
            
            $response = $client->request('PUT', 'https://accounts.cartx.io/api/'.$shop->cartx_app->domain.'/orders/'.$supplier_order->order->external_id.'/fulfill', [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer '.$shop->cartx_app->token,
                    'Accept' => 'application/json',
                ]]);
                
			if($response->getStatusCode() == 200){
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

	public static function cancelFulfillment($supplier_order, $shipping){
        //ATÉ ENTÃO A CARTX NÃO SUPORTA ESSA OPERAÇÃO
        return true;
        //$shop = $supplier_order->order->shop;

		// try {
		// 	$client = new \GuzzleHttp\Client();
        //     $response = $client->request('POST', 'https://'.$shop->shopify_app->app_key.':'.$shop->shopify_app->app_password.'@'.$shop->shopify_app->domain.'.myshopify.com/admin/api/2021-04/fulfillments/'.$shipping->external_fulfillment_id.'/cancel.json');

        //     $data = [
		// 		'fulfillment' => (object)[
        //             'id' => $shipping->external_fulfillment_id,
		// 			'notify_customer' => 1,
		// 			'tracking_company' => '',
        //             'tracking_numbers' => [],
        //             'tracking_urls' => [],
		// 		]
        //     ];

        //     $response = $client->request('PUT', 'https://accounts.cartx.io/api/'.$shop->cartx_app->domain.'/orders/'.$supplier_order->order->external_id.'/fulfill', [
        //         'json' => $data,
        //         'headers' => [
        //             'Authorization' => 'Bearer '.$shop->cartx_app->token,
        //             'Accept' => 'application/json',
        //         ]]);

		// 	if($response->getStatusCode() == 200){
		// 		return true;
		// 	}else{
		// 		return false;
		// 	}
		// } catch (\Exception $e) {
		//     report($e);
        //     ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

        //     return false;
		// }
	}
}
