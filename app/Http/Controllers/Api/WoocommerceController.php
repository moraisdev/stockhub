<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use App\Services\Shop\WoocommerceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Orders;

use App\Models\WebhookCalls;

class WoocommerceController extends Controller
{
    public function importOrder(Request $request){
        try {
            WebhookCalls::create([
                'identifier' => 'woocommerce.import-order',
                'content' => json_encode($request->all())
            ]);

            $order_id = $request->id;
            $shop_domain = $request->header('X-Woocommerce-Shop-Domain');
            $shop = Shops::whereHas('woocommerce_app', function($q) use ($shop_domain){
                // $q->where('domain', str_replace('.mywoocommerce.com', '', $shop_domain));
            })->first();

            if($shop){
                $client = new \GuzzleHttp\Client([
                    // Base URI is used with relative requests
                    'base_uri' => $shop->woocommerce_app->domain,
                ]);
                $response = $client->request('GET', '/wp-json/wc/v3/orders/'.$order_id, [
                    'headers' => [
                        "Authorization" => "Basic ". base64_encode($shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password)
                    ],
                    'verify' => false, //only needed if you are facing SSL certificate issue
                ]);

                if($response->getStatusCode() == 200){
                    $order = json_decode($response->getBody())->order;

                    if($order){
                        $result = WoocommerceService::registerOrder($shop, $order);
                        if($result){
                            return response()->json(['msg' => 'Ordem criada com sucesso.'], 200);
                        }else{
                            //verifica se a ordem ja foi inserida, caso tenha sido, não retorna erro
                            $verifyOrder = Orders::where('external_id', $order_id)
                                                ->where('shop_id', $shop->id)
                                                ->first();
                            if(!$verifyOrder){
                                Log::error('Webhook Error - Woocommerce - Erro ao inserir ordem - Loja: '.$shop->name.' > Ordem: '.$order->name);
                            }                            
                            //return response()->json(['error' => 'Erro ao inserir ordem.'], 400);
                        }
                    }
                }
                Log::error('Webhook Error - Woocommerce - Ordem inválida - domain: '.$shop_domain);
                //return response()->json(['error' => 'Ordem inválida'], 400);
            }else{
                Log::error('Webhook Error - Woocommerce - Loja inválida domain: '.$shop_domain.' > id: '.$order_id);
                //return response()->json(['error' => 'Webhook Error - Woocommerce - Loja ou ordem inválida'], 500);
            }
        } catch(\Exception $e){
            Log::error('Webhook Error - Woocommerce - Loja ou ordem inválida domain: '.$shop_domain.' > id: '.$order_id);
            report($e);
            return response()->json(['error' => 'Webhook Error - Woocommerce - Loja ou ordem inválida'], 500);
        }
    }

    public function cancelOrder(Request $request){
        try {
            WebhookCalls::create([
                'identifier' => 'woocommerce.cancel-order',
                'content' => json_encode($request->all())
            ]);

            $order_id = $request->id;
            $shop_domain = $request->header('X-Woocommerce-Shop-Domain');
            //$token_webhook = $request->header('X-Woocommerce-Hmac-SHA256');

            $shop = Shops::whereHas('woocommerce_app', function($q) use ($shop_domain){
                $q->where('domain', str_replace('.mywoocommerce.com', '', $shop_domain));
            })->first();

            if($shop){                
                $client = new \GuzzleHttp\Client([
                    // Base URI is used with relative requests
                    'base_uri' => $shop->woocommerce_app->domain,
                ]);
                $response = $client->request('GET', '/wp-json/wc/v3/orders/'.$order_id, [
                    'headers' => [
                        "Authorization" => "Basic ". base64_encode($shop->woocommerce_app->app_key.':'.$shop->woocommerce_app->app_password)
                    ],
                    'verify' => false, //only needed if you are facing SSL certificate issue
                ]);

                if($response->getStatusCode() == 200){
                    $order = json_decode($response->getBody())->order;

                    if($order && $order->cancelled_at){ //caso seja uma ordem que foi cancelada
                        //cancela a ordem
                        //se a ordem existe e está pendente na mawa
                        $orderCancelled = Orders::where('external_id', $order_id)->where('external_service', 'woocommerce')->where('status', 'pending')->first();
                        if($orderCancelled){
                            if($orderCancelled->supplier_orders->count() < 1){
                                $orderCancelled->status = 'canceled';
                                $orderCancelled->save();
                            }         
                        }
                        
                    }
                }
            }else{
                Log::error('Import paid order through webhook fail.');

                response('Import paid order through webhook fail.', 500);
            }

            
        } catch(\Exception $e){
            report($e);
        }
    }
}
