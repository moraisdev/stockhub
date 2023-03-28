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
use App\Services\CorreiosService;
use App\Services\TotalExpressService;
use App\Services\ChinaShippingService;
use Illuminate\Support\Facades\Log;
use App\Services\MelhorEnvioService;

class BlingServiceShop{

    public static function getPaidOrders($shop){
	    try{
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', "https://bling.com.br/Api/v2/pedidos/json/&apikey=".$shop->bling_apikey);
           
            if($response->getStatusCode() == 200){
                $orders = json_decode($response->getBody())->retorno->pedidos;
            }

          
         
            return ['status' => '200', 'message' => 'Pedidos buscados no Bling com sucesso.', 'data' => $orders];
      
      
      
        }catch(\Exception $e){
	        if($e->getCode() == 401){
                return ['status' => '401', 'message' => 'Não  conseguimos buscar seus pedidos no Bling. Verifique a configuração corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
              
            }else{

	            report($e);
                Log::error($e);
                ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
                if($e->getCode() == 403){
                    return ['status' => '403', 'message' => 'O Usuario não tem permissão para buscar os pedidos, de permissão ao seu app no bling.'];
                } 
               
            }
        }
	}

    public static function registerOrder($shop, $bling_order){

        try {
              
             
                $order = Orders::firstOrNew(['shop_id' => $shop->id, 'external_id' => $bling_order->pedido->numero]);
                  
                $order = new Orders();
                $order->external_service = 'bling_service';
                $order->name = $bling_order->pedido->numero;
                $order->email = $bling_order->pedido->cliente->email;
                $order->external_price = $bling_order->pedido->totalprodutos;
                $order->external_id = $bling_order->pedido->numero;
                $order->status = 'pending';
                $order->shop_id = $shop->id;
                $order->external_created_at = date('Y-m-d h:i:s', strtotime($bling_order->pedido->data));
               // $order->customer_id = $customer->id;
                $order->save();  

              
                $items = self::registerItems($order, $bling_order);     

                 $customer = self::registerCustomer($shop, $bling_order->pedido->cliente);
                 $order->customer_id = $customer->id;
                 $order->save();
                 
                 
                 $rastreamento = $bling_order->pedido->transporte->volumes[0]->volume;
               
                    $shipping = self::registerShipping($order, $items['items'], $customer , $rastreamento);

                 
                 
        
                

                if(!$customer || !$items || !$shipping || $items['total_amount'] == 0){
                    $order->external_id = null;
                    $order->save();
                    $order->delete();

                    return null;
                }

                
                
                $order->items_amount = $items['total_amount'];
                $order->shipping_amount = $shipping['total_shipping_amount'];
                $order->amount = $items['total_amount'] + $shipping['total_shipping_amount'];
                $order->tracking_number =  $rastreamento->codigoRastreamento;
                $order->tracking_url =  $rastreamento->urlRastreamento;
                $order->tracking_servico =  $rastreamento->servico;              
                   
                
                
                $order->save();


           



                return $order;
                


            } catch(\Exception $e){
                    report($e);
                    Log::error($e);
                   
                    ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
                }
            }


    


    public static function registerItems($order, $bling_order){
       
            $items = array();
            $total_amount = 0;
            
            foreach ($bling_order->pedido->itens as $bling_item) {     

                                         
                                                        
                $variant = ProductVariants::where('sku', $bling_item->item->codigo)->first();
                
                $aplicatedDiscount = 0; //desconto aplicado
                $orderItemDiscount = NULL; //salva o desconto usado no item (caso exista)

                if($variant){
                    $variant_id = $variant->id;
                    $charge_or_not = 1;   
                    $amount = ($variant->price * $bling_item->item->quantidade);
                    $amount = $amount - ($amount * ($aplicatedDiscount/100));

                    $total_amount += $amount;
                }else{
                    continue;
                }
                
                $item = new OrderItems();    
                $item->order_id = $order->id;
                $item->product_variant_id = $variant_id;
                $item->external_service = 'bling_service';
                $item->external_product_id = $bling_item->item->codigo;
                $item->external_variant_id = $bling_item->item->codigo;
                $item->sku = $bling_item->item->codigo;
                $item->title = $bling_item->item->descricao;
                $item->quantity = $bling_item->item->quantidade;
                $item->amount = $amount;
                $item->external_price = $bling_item->item->valorunidade;
                $item->charge = $charge_or_not;

                $item->save();

                if($orderItemDiscount){ //caso haja desconto, salva o item o OrderItem
                    $orderItemDiscount->order_item_id = $item->id;
                    $orderItemDiscount->save();
                }             
                

                array_push($items, $item);
            

                }                
            
            return ['items' => $items, 'total_amount' => $total_amount];
    }

    public static function registerCustomer($shop, $bling_cliente){
        
      
        try {
            $customer = Customers::firstOrCreate(['shop_id' => $shop->id, 'external_id' => $bling_cliente->id]);

            $customer->external_service = 'bling_service';
            $customer->first_name = $bling_cliente->nome;
            $customer->email = $bling_cliente->email;
                  

            if(!$customer->save()){
                return null;
            }

            $address = self::registerCustomerAddress($customer, $bling_cliente);
            
            if(!$address){
                return null;
            }

            
            return $customer;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }

        
	}

	public static function registerCustomerAddress($customer, $bling_cliente){
        try {
            $address = CustomerAddresses::firstOrCreate(['customer_id' => $customer->id, 'address1' => $bling_cliente->endereco]);

            $address->name = $bling_cliente->nome;
            $address->address2 = $bling_cliente->bairro;
            $address->city = $bling_cliente->cidade;
            $address->province = $bling_cliente->uf;
            $address->zipcode = $bling_cliente->cep;
            $address->phone = $bling_cliente->celular;
            $address->province_code = $bling_cliente->uf;
            $address->number = $bling_cliente->numero;
            $address->complement  = $bling_cliente->complemento;
            $address->country = 'Brazil';     
           

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

    public static function registerShipping($order, $items, $customer , $rastreamento){
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

                   if($rastreamento != '' ){
                        $shipping->amount = '0.00';
                        $shipping->save();
                        $total_shipping_amount = '0.00' ;

                    } else {
                        $shipping->amount = $shipping_amount;
                        $shipping->save();
                        $shippings[] = $shipping;
                        $total_shipping_amount += $shipping_amount;


                    }
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

public function checkOrderTrackingNumber($order_id , $blingkey){
        //percorre a lista de ids e verifica se tem o código de rastreio
        $order = Orders::where('id', $order_id)->first();
        
        try{
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', "https://bling.com.br/Api/v2/pedido/".$order->external_id."/json/&apikey=".$blingkey);
           
            if($response->getStatusCode() == 200){
                $response = json_decode($response->getBody())->retorno->pedidos[0]->pedido->transporte->volumes[0]->volume;
                return $response;
            }
                   
        }catch(\Exception $e){
	        if($e->getCode() == 401){
                return ['status' => 'error', 'message' => 'Não conseguimos buscar seus pedido no Bling. Verifique a configuração corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            }else{
	            report($e);
                Log::error($e);
                ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            }
        }
	}
	

	
}
