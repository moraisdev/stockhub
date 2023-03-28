<?php

namespace App\Services\Shop;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Models\ErrorLogs;
use App\Models\Mercadolivreapi;
use Illuminate\Http\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Dsc\MercadoLivre\Meli;
use Dsc\MercadoLivre\Resources\Authorization\AuthorizationService;
use App\Models\ProductVariantStock;
use App\Models\Orders;
use App\Models\ShopProducts;
use App\Models\OrderItems;
use App\Models\ProductVariants;
use App\Models\Customers;
use App\Models\CustomerAddresses;





class MercadolivreService{


    public static function getCode($shop){

       

    }


    public static function getToken($shop, $apimercadolivre){

    try{
        $meli = new Meli($apimercadolivre->app_id, $apimercadolivre->secret_id);
        $service = new AuthorizationService($meli);        
        $token = $service->getAccessToken();
        return $token;

    }catch(\Exception $e){
          return null;
        
    }
}   

    public static function getBuscaCategoria($shop, $apimercadolivre  , $product){

        try{
            $title = substr($product->title, 0,60);    
            $client = new \GuzzleHttp\Client();
        
            $response = $client->request('GET', 'https://api.mercadolibre.com/sites/MLB/search?q='.$title,
        ['headers' => [
            'Authorization' => 'Bearer '.$apimercadolivre->token,
            'Accept' => 'application/json',
        ]
        ]);

        $orders = json_decode($response->getBody());
        return $orders->results[0];

      //  return ['status' => 'success', 'message' => 'Pedidos buscados no Bling com sucesso.'];
    }catch(\Exception $e){
        if($e->getCode() == 401){
          //  return ['status' => 'error', 'message' => 'Token Expirado reliaze a atualização do token.'];
          return $e;
        }else{
            report($e);
            Log::error($e);
           // ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
           return $e;
        }
    }

    }


    public static function postProduto($shop, $apimercadolivre  , $product ,$searchprodutoml ,$productimagens){

       
        try{
            
            
            $dados['title']                 = substr($product->title, 0,60);
            $dados['category_id']           = $searchprodutoml->category_id;
            $dados['price']                 = $product->variants[0]->price;
            $dados['currency_id']           = "BRL";
            $dados['available_quantity']    =  1;
            $dados['buying_mode']           = "buy_it_now";
            $dados['condition']             = "new";
            $dados['listing_type_id']       = "gold_pro";
           
            if(isset($productimagens[0]->src)){
                $dados['pictures'][0]['source'] = $productimagens[0]->src;

            }
            if(isset($productimagens[1]->src)){
                $dados['pictures'][1]['source'] = $productimagens[1]->src;
            }
            
            if(isset($productimagens[2]->src)){
                $dados['pictures'][2]['source'] = $productimagens[2]->src;

            }
            
            if(isset($productimagens[3]->src)){
                $dados['pictures'][3]['source'] = $productimagens[3]->src;


            }    


            
               $attributesgtin[] = [  

              
                "id" =>  "GTIN",
                "value_name" => $product->ean_gtin,

                   			 	   
                                      
                
            ];
			
			 if(isset($product->sku)){ 
				  $attributessku[] = [  

              
                "id" =>  "SELLER_SKU",
                "value_name" => $product->sku,                            
                
            ];
				 
			 }
             
             if(isset($product->variants[0]->sku)){

                $attributessku[] = [  

              
                    "id" =>  "SELLER_SKU",
                    "value_name" => $product->variants[0]->sku,                            
                    
                ];
             }   

           
            if (($product->sku <> null) and ($product->ean_gtin <> null)){
                $dados['attributes'] = array_merge($searchprodutoml->attributes ,   $attributesgtin , $attributessku ) ;
            }elseif ((($product->variants[0]->sku <> null) and ($product->ean_gtin <> null))) {
                $dados['attributes'] = array_merge($searchprodutoml->attributes ,   $attributesgtin , $attributessku ) ;
			}else{

                $dados['attributes'] = $searchprodutoml->attributes;

            } 
           
           
            $dados['shipping']['free_shipping']       = "true"; 
            $dados['shipping']['mode']       = "me2"; 

            //dd($dados);
            
                        
                        $client = new \GuzzleHttp\Client();
                        $response = $client->request('POST', 'https://api.mercadolibre.com/items',
                        [  
                            'json' => $dados,
                            'headers' => [
                        'Authorization' => 'Bearer '.$apimercadolivre->token,
                        'Accept' => 'application/json',
                       ]
                    ]);

        $postproduto = json_decode($response->getBody());
        $status = json_decode($response->getStatusCode()); 
        return  ['code' => $status , 'anuncio' => $postproduto];

        
       // return $postproduto;




      //  return ['status' => 'success', 'message' => 'Pedidos buscados no Bling com sucesso.'];
    }catch(\GuzzleHttp\Exception\RequestException $e){
       
        if($e->getCode() == 401){
           return ['status' => 'error', 'message' => 'Não  conseguimos Publicar o Anuncio MercadoLivre. Verifique a configuração corretamente. Em caso de dúvidas entre em contato com nosso suporte.' , 'code' => '401'];
          
        }else{
            report($e);
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
             throw  $e;
            
        }
    }

    }


    public static function putProduto($shopproduct2, $apimercadolivre  , $product ){

      $busca = ProductVariantStock::where('product_variant_id' , $product->variants[0]->id)->first();  
      
    
        try{
           
            if($shopproduct2->ml_product_id <> null){
                         
                        $dados['plain_text']  = $product->description;           
            
                        
                        $client = new \GuzzleHttp\Client();
                        $response = $client->request('PUT', 'https://api.mercadolibre.com/items/'.$shopproduct2->ml_product_id.'/description',
                        [  
                            'json' => $dados,
                            'headers' => [
                        'Authorization' => 'Bearer '.$apimercadolivre->token,
                        'Accept' => 'application/json',
                       ]
                    ]);

                    
                    $postproduto = json_decode($response->getBody());        
                    return $postproduto;
        }



      //  return ['status' => 'success', 'message' => 'Pedidos buscados no Bling com sucesso.'];
    }catch(\GuzzleHttp\Exception\RequestException $e){
        if($e->getCode() == 401){
           return ['status' => 'error', 'message' => 'Não  conseguimos buscar seus pedidos no Mercadolivre. Verifique a configuração corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
          
        }else{
            report($e);
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
             throw  $e;
            
        }
    }

    }


    public static function putEstoque($shopproduct, $apimercadolivre  , $product ){

        
        
        
          try{
            $busca = ProductVariantStock::where('product_variant_id' , $product->variants[0]->id)->first();
             
            //dd($busca);
              if (($shopproduct->ml_product_id <> null) and ($busca <> null)){
                           
                          $dados['available_quantity']  = $busca->quantity;           
              
                          
                          $client = new \GuzzleHttp\Client();
                          $response = $client->request('PUT', 'https://api.mercadolibre.com/items/'.$shopproduct->ml_product_id,
                          [  
                              'json' => $dados,
                              'headers' => [
                          'Authorization' => 'Bearer '.$apimercadolivre->token,
                          'Accept' => 'application/json',
                         ]
                      ]);
  
                      
                      $postproduto = json_decode($response->getBody());
                      $status = json_decode($response->getStatusCode());       
                      return  ['status' => $status];
          }else {

            return ['status' => 'error', 'message' => 'Não  conseguimos atualizar o estoque do produto no Mercadolivre. Em caso de dúvidas entre em contato com nosso suporte.'];

          }
  
  
  
        //  return ['status' => 'success', 'message' => 'Pedidos buscados no Bling com sucesso.'];
      }catch(\GuzzleHttp\Exception\RequestException $e){
        
          if($e->getCode() == 401){
             return ['status' => 'error', 'message' => 'Não  conseguimos buscar seus pedidos no Mercadolivre. Verifique a configuração corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            
          }elseif($e->getCode() == 400){

            return ['status' => $e->getCode()];
          }else{
              report($e);
              Log::error($e);
              ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
               throw  $e;
              
          }
          
      }
  
      }


    public static function getOrder($apimercadolivre ){

       
        
      
          try{

                          $client = new \GuzzleHttp\Client();
                         // $response = $client->request('GET', 'https://api.mercadolibre.com/orders/search?seller='.$shopproductml->seller_id_ml.'&order.status=paid',
                          $response = $client->request('GET', 'https://api.mercadolibre.com/orders/search/recent?seller='.$apimercadolivre->seller_id_ml.'&order.status=paid',
                         
                         [  
                             
                              'headers' => [
                          'Authorization' => 'Bearer '.$apimercadolivre->token,
                          'Accept' => 'application/json',
                         ]
                      ]);
  
                      
                      $getorder = json_decode($response->getBody());        
                      
                      $status = json_decode($response->getStatusCode());       
                      return  ['status' => $status , 'order' => $getorder ];
                    //  return $getorder->results;

                     // dd($getorder->results);
        
  
  
  
        //  return ['status' => 'success', 'message' => 'Pedidos buscados no Bling com sucesso.'];
      }catch(\GuzzleHttp\Exception\RequestException $e){
          if($e->getCode() == 401){           
            return ['status' => 'error', 'message' => 'Não  conseguimos buscar seus pedidos no mercadolivre. Verifique a configuração corretamente. Em caso de dúvidas entre em contato com nosso suporte.', $e->getCode()];
            
          }else{
              report($e);
              Log::error($e);
              ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
               throw  $e;
             // dd($e); 
              
          }
      }
  
      }




      public static function getAnuncio($apimercadolivre , $order){

        try{       
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('GET', 'https://api.mercadolibre.com/shipments/'.$order->shipping_ml,
                      [  
                              
                        'headers' => [
                        'Authorization' => 'Bearer '.$apimercadolivre->token,
                        'Accept' => 'application/json',
                         ]
                      ]);
  
                      
                      $getanuncio = json_decode($response->getBody());        
                      $status = json_decode($response->getStatusCode());       
                      return  ['status' => $status , 'anuncio' => $getanuncio ];
                     
          
  
  
    }catch(\GuzzleHttp\Exception\RequestException $e){
     if($e->getCode() == 404){           
        return ['status' => $e->getCode()];
                          
    }else{
        report($e);
        Log::error($e);
        ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        throw  $e;
        return ['status' => $e->getCode()];
                            
     }
    }
                
}



      public static function registerOrder($shop, $order_ml){
        
       
        try {              
                $order = Orders::firstOrNew(['shop_id' => $shop->id, 'external_id' => $order_ml->id]);
                
                if($order->id != null){
                    return true;
                }
    
                $order->external_service = 'mercadolivre';
                $order->name = $order_ml->buyer->nickname;
                $order->external_price = $order_ml->total_amount;
                //$order->external_usd_price = $cartx_order->total_price_usd; (não nulo, mudei manualmente no bd local)
               // $order->landing_site = $order_ml->landing_site;
                $order->status = 'pending';
                $order->external_created_at = date('Y-m-d h:i:s', strtotime($order_ml->last_updated));
                $order->shipping_ml = $order_ml->shipping->id;
    
                if(!$order->save()){
                    return null;
                }
    
              
                $items = self::registerItems($order, $order_ml->order_items); 
                $customer = self::registerCustomer($shop, $order_ml);

                if($customer == 403){

                    $customer = self::registerCustomer2($shop, $order_ml);

                }
                
                $order->customer_id = $customer->id;
                //$order->items_amount = $items['total_amount'];
                 $order->shipping_amount = 0.00;
              //  $order->amount = $items['total_amount'];
                $order->save();
                return $order;
            }
            
         catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
         //  dd($e);
        }
    }



    public static function registerItems($order, $ml_line_items){
        try {
            $items = [];
            $total_amount = 0;
            $charge_or_not = 1;

          

            foreach ($ml_line_items as $ml_item) {
               
                $shopproduct = ShopProducts::where('ml_product_id', $ml_item->item->id)->first();
                $variant = ProductVariants::where('product_id', $shopproduct->product_id)->first();
                $amount = ($variant->price * $ml_item->quantity);
               
                $item = new OrderItems();               
                $item->order_id = $order->id;
                $item->product_variant_id = $variant->id;
                $item->external_service = 'mercadolivre';
                $item->external_product_id = $ml_item->item->id;
                $item->external_variant_id = $ml_item->item->id;
                $item->sku = $variant->sku;
                $item->title = $ml_item->item->title;
                $item->quantity = $ml_item->quantity;
                $item->amount = $amount;
                $item->external_price = $ml_item->unit_price;
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

    public static function registerCustomer($shop, $order_ml){
       
       
        try {

            

            $apimercadolivre = Mercadolivreapi::where('shop_id',$shop->id )->first();
            $client = new \GuzzleHttp\Client();
            // $response = $client->request('GET', 'https://api.mercadolibre.com/orders/search?seller='.$shopproductml->seller_id_ml.'&order.status=paid',
             $response = $client->request('GET', 'https://api.mercadolibre.com/users/'.$order_ml->buyer->id.'/addresses',
            
            [  
                
             'headers' => [
             'Authorization' => 'Bearer '.$apimercadolivre->token,
             'Accept' => 'application/json',
            ]
         ]);

         
         $getanddress = json_decode($response->getBody());     
            $ml_customer = $getanddress[0];
            $customer = Customers::firstOrCreate(['shop_id' => $shop->id, 'external_id' => $order_ml->buyer->id]);
            $customer->external_service = 'mercadolivre';
            $customer->first_name = $ml_customer->contact;
            $customer->total_spent = 0.00;
           
          

          
            
          $customer->save();

         

            $address = self::registerCustomerAddress($customer, $ml_customer);

            
            return $customer;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            if($e->getCode() == 403){  

                return $e->getCode();

            }    
        }
	}


    public static function registerCustomerAddress($customer, $ml_address){
        try {

           
            $address = CustomerAddresses::firstOrCreate(['customer_id' => $customer->id, 'address1' => $ml_address->address_line]);

            if ($ml_address <> null){

            $address->name = $ml_address->contact;
            $address->city = $ml_address->city->name;
            $address->province = $ml_address->state->name;
            $address->country = $ml_address->country->name;
            $address->zipcode = $ml_address->zip_code;
            $address->phone = $ml_address->phone;
            $address->province_code = $ml_address->state->id;
            $address->country_code = $ml_address->country->id;

            }
            

            $address->save();            

            return $address;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }


    public static function registerCustomer2($shop, $order_ml){
       
        
       
        try {

            $customer = new Customers();
            $customer->shop_id = $shop->id;
            $customer->external_id = $order_ml->buyer->id;
            $customer->external_service = 'mercadolivre';
            $customer->first_name = $order_ml->buyer->nickname;
            $customer->save();

            $address = self::registerCustomerAddress2($customer, $order_ml);


            return $customer;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
          // dd($e);
        }
    } 

    public static function registerCustomerAddress2($customer, $ml_address){
        try {

           
            $address = CustomerAddresses::firstOrCreate(['customer_id' => $customer->id, 'name' => $ml_address->buyer->nickname ]);
            $address->save();            

            return $address;
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }


    public static function imprimirEtiqueta($apimercadolivre, $order){
        //dd($order);
		try {

            $client = new \GuzzleHttp\Client();
			$response = $client->request('GET', 'https://api.mercadolibre.com/shipment_labels?shipment_ids='.$order->shipping_ml.'&response_type=pdf',
            [  
                
                 'headers' => [
             'Authorization' => 'Bearer '.$apimercadolivre->token,
             'Cache-Control' => 'no-cache', 
            'Content-Type' => 'application/pdf',
           
					 
            ]
         ]);

			$lengthArray = $response->getHeader('Content-Length');
            $length = $lengthArray[0];
			header('Content-Type: application/pdf; charset=utf-8');
            header('Content-Length: '.$length);
            header("Content-Disposition: inline; filename='details.pdf'");

            echo $response->getBody()->getContents();	                
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
			 if($e->getCode() == 400){  

                return $e->getCode();

            }    
        }
    }



    public static function getProduto($shopproduct, $apimercadolivre , $product){

        //dd($shopproduct);
        try{       
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('GET', 'https://api.mercadolibre.com/items/'.$shopproduct->ml_product_id,
                      [  
                              
                        'headers' => [
                        'Authorization' => 'Bearer '.$apimercadolivre->token,
                        'Accept' => 'application/json',
                         ]
                      ]);
  
                      
                      $getanuncio = json_decode($response->getBody());        
                      $status = json_decode($response->getStatusCode());       
                      return  ['status' => $status , 'anuncio' => $getanuncio ];
                     
          
  
  
    }catch(\GuzzleHttp\Exception\RequestException $e){
     if($e->getCode() == 404){           
        return ['status' => $e->getCode()];
                          
    }else{
        report($e);
        Log::error($e);
        ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        throw  $e;
        return ['status' => $e->getCode()];
                            
     }
    }
                
}


public static function getusuario($shop, $apimercadolivre){

    try{
       
        $client = new \GuzzleHttp\Client();
    
        $response = $client->request('GET', 'https://api.mercadolibre.com/users/me',
    ['headers' => [
        'Authorization' => 'Bearer '.$apimercadolivre->token,
        'Accept' => 'application/json',
    ]
    ]);

    $usuario = json_decode($response->getBody());
    return $usuario;

  //  return ['status' => 'success', 'message' => 'Pedidos buscados no Bling com sucesso.'];
}catch(\Exception $e){
    if($e->getCode() == 401){
      //  return ['status' => 'error', 'message' => 'Token Expirado reliaze a atualização do token.'];
      return $e;
    }else{
        report($e);
        Log::error($e);
       // ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
       return $e;
    }
}

}


}    