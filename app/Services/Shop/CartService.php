<?php

namespace App\Services\Shop;

use App\Models\ErrorLogs;
use App\Models\OrderShippings;
use App\Models\Orders;

use App\Models\Customers;
use App\Models\CustomerAddresses;

use App\Models\Suppliers;
use App\Services\CorreiosService;
use App\Services\TotalExpressService;
use App\Services\MelhorEnvioService;
use App\Services\ChinaShippingService;

class CartService{

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

}