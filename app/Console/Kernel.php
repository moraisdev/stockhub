<?php

namespace App\Console;

use App\Models\FreteMelhorEnvio;
use App\Models\Shops;
use App\Models\ShopShopifyWebhooks;
use App\Models\SupplierOrders;
use App\Models\SupplierOrderShippings;
use App\Services\BlingService;
use App\Services\MelhorEnvioService;
use App\Services\Shop\ShopifyService;
use GuzzleHttp\Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            //cronjob para buscar todos os fretes na melhor envio e verificar se já foi enviado e tem etiqueta e link
            $fretesMelhorEnvio = FreteMelhorEnvio::where('status', 'pending')
                ->orWhere('status', 'released')
                //->where('created_at', '>=', date("Y-m-d H:i:s", strtotime('-7 days', strtotime(date("Y-m-d H:i:s"))))) //pega só no intervalo de 7 dias
                ->orderBy('id', 'desc')
                ->limit(100)
                ->get();
            //dd($fretesMelhorEnvio);

            $melhorEnvioService = new MelhorEnvioService();

            foreach ($fretesMelhorEnvio as $freteMelhorEnvio) {
                $responseFrete = $melhorEnvioService->updateStatusFreight($freteMelhorEnvio->melhor_envio_id);

                if ($responseFrete && isset($responseFrete->status)) {
                    $freteMelhorEnvio->status = $responseFrete->status;

                    if (!$responseFrete->tracking) {//gera a etiqueta
                        $responseTag = $melhorEnvioService->generateTag($freteMelhorEnvio->melhor_envio_id);

                        if ($responseTag) { //caso tenha gerado a etiqueta com sucesso
                            //pega o número de rastreio e pega o link da etiqueta
                            $responseFrete = $melhorEnvioService->updateStatusFreight($freteMelhorEnvio->melhor_envio_id);
                            if ($responseFrete && $responseFrete->tracking) {
                                $freteMelhorEnvio->tracking = $responseFrete->tracking;
                            }
                        }
                    } else { //caso ja tenha gerado a etiqueta, só pega o número do rastreio
                        $freteMelhorEnvio->tracking = $responseFrete->tracking;
                    }

                    //imprime a etiqueta
                    $linkTag = $melhorEnvioService->printTag($freteMelhorEnvio->melhor_envio_id);
                    if ($linkTag) {
                        $freteMelhorEnvio->tag_url = $linkTag;
                    }

                    if ($freteMelhorEnvio->save()) {
                        //já aproveita e atualiza o código de rastreio oficial
                        $freteMelhorEnvio->supplier_order->shipping->tracking_number = $freteMelhorEnvio->tracking;
                        $freteMelhorEnvio->supplier_order->shipping->company = $responseFrete->service->name;
                        $freteMelhorEnvio->supplier_order->shipping->tracking_url = 'https://www.melhorrastreio.com.br/rastreio/' . $freteMelhorEnvio->tracking;
                        if ($freteMelhorEnvio->supplier_order->shipping->save()) {
                            //atualiza o status do frete automaticamente na shopify e cartx
                            if ($freteMelhorEnvio->status == 'posted') {
                                $freteMelhorEnvio->supplier_order->shipping->status = 'sent';

                                if ($freteMelhorEnvio->supplier_order->shipping->save()) {
                                    \App\Http\Controllers\Supplier\OrdersController::updateAutomaticShippingMelhorEnvio($freteMelhorEnvio->supplier_order->id, $freteMelhorEnvio->supplier_order->supplier);
                                }
                            }
                        }
                    }
                }
            }
        })->everyMinute();

        $schedule->call(function () {
            $bling_service = new BlingService();
            $orders = SupplierOrders::where('status', 'paid')
                ->where('exported_to_bling', 0)
                ->whereHas('shipping', function ($q) {
                    $q->where('status', 'pending');
                })->get();

            foreach ($orders as $o) {
                if ($bling_service->checkSendOrder($o)) {
                    $trackingNumberCorreios = $bling_service->generateOrder($o);

                    if ($trackingNumberCorreios) { //caso seja um código válido, salva
                        $shipping = SupplierOrderShippings::where('supplier_id', $o->supplier_id)
                            ->where('supplier_order_id', $o->id)
                            ->first();
                        if ($shipping) {
                            //$shipping->status = 'sent';
                            $shipping->tracking_url = "https://www2.correios.com.br/sistemas/rastreamento/default.cfm/";
                            $shipping->tracking_number = $trackingNumberCorreios;
                            $shipping->save();
                            //echo "código: ".$trackingNumberCorreios."<br>";
                        }
                    }

                    $o->exported_to_bling = 1;
                    $o->save();
                }
            }
        })->everyMinute();

        $schedule->call(function () {

            $permissions = [
                'orders/paid' => url('api/shopify/webhooks/orders/paid')
            ];

            $shops = Shops::with('shopify_app', 'shopify_webhooks')->has('shopify_app')->whereDoesntHave('shopify_webhooks', function ($q) use ($permissions) {
                $q->whereIn('topic', array_keys($permissions));
            })->get();

            foreach ($shops as $shop) {
                // If shop doenst have orders/paid webhook, register it.

                if ($shop->shopify_webhooks->where('topic', 'orders/paid')->count() == 0) {
                    $data = [
                        'webhook' => [
                            'topic' => 'orders/paid',
                            'address' => url('api/shopify/webhooks/orders/paid'),
                            'format' => 'json'
                        ]
                    ];

                    try {
                        $response = ShopifyService::GuzzleCalls($shop,'POST','webhooks.json',false,false,$data);

                        if ($response->getStatusCode() == 201) {
                            /* Register webhook line */
                            $webhook = new ShopShopifyWebhooks();

                            $webhook->shop_id = $shop->id;
                            $webhook->topic = 'orders/paid';
                            $webhook->address = url('api/shopify/webhooks/orders/paid');
                            $webhook->format = 'json';

                            $webhook->save();
                        } else {
                            /* Register webhook line */
                            $webhook = new ShopShopifyWebhooks();

                            $webhook->shop_id = $shop->id;
                            $webhook->topic = 'orders/paid';
                            $webhook->address = url('api/shopify/webhooks/orders/paid');
                            $webhook->format = 'json';
                            $webhook->success = 0;

                            $webhook->save();
                        }
                    } catch (\Exception $e) {
                        /* Register webhook line */
                        $webhook = new ShopShopifyWebhooks();

                        $webhook->shop_id = $shop->id;
                        $webhook->topic = 'orders/paid';
                        $webhook->address = url('api/shopify/webhooks/orders/paid');
                        $webhook->format = 'json';
                        $webhook->success = 0;

                        if ($e->getCode() == 422) {
                            $webhook->success = 1;
                        }

                        $webhook->save();

                        report($e);
                    }
                }
            }
        })->everyMinute();

        // //cron job para salvar o webhook de cancelamento caso ainda não tenha sido cadastrado
        // $schedule->call(function(){
        //     $permissions = [
        //         'orders/cancelled' => url('api/shopify/webhooks/orders/cancelled')
        //     ];

        //     $shops = Shops::with('shopify_app', 'shopify_webhooks')->has('shopify_app')->whereDoesntHave('shopify_webhooks', function($q) use ($permissions){
        //         $q->whereIn('topic', array_keys($permissions));
        //     })->get();

        //     foreach($shops as $shop){

        //         // If shop doenst have orders/cancelled webhook, register it.
        //         if($shop->shopify_webhooks->where('topic', 'orders/cancelled')->count() == 0){
        //             $data = [
        //                 'webhook' => [
        //                     'topic' => 'orders/cancelled',
        //                     'address' => url('api/shopify/webhooks/orders/cancelled'),
        //                     'format' => 'json'
        //                 ]
        //             ];

        //             try {
        //                 $client = new \GuzzleHttp\Client();
        //                 $response = $client->request('POST', 'https://'.$shop->shopify_app->app_key.':'.$shop->shopify_app->app_password.'@'.$shop->shopify_app->domain.'.myshopify.com/admin/api/2021-04/webhooks.json', ['json' => $data]);

        //                 if($response->getStatusCode() == 201){
        //                     /* Register webhook line */
        //                     $webhook = new ShopShopifyWebhooks();

        //                     $webhook->shop_id = $shop->id;
        //                     $webhook->topic = 'orders/cancelled';
        //                     $webhook->address = url('api/shopify/webhooks/orders/cancelled');
        //                     $webhook->format = 'json';

        //                     $webhook->save();
        //                 }else{
        //                     /* Register webhook line */
        //                     $webhook = new ShopShopifyWebhooks();

        //                     $webhook->shop_id = $shop->id;
        //                     $webhook->topic = 'orders/cancelled';
        //                     $webhook->address = url('api/shopify/webhooks/orders/cancelled');
        //                     $webhook->format = 'json';
        //                     $webhook->success = 0;

        //                     $webhook->save();
        //                 }
        //             } catch(\Exception $e){
        //                 /* Register webhook line */
        //                 $webhook = new ShopShopifyWebhooks();

        //                 $webhook->shop_id = $shop->id;
        //                 $webhook->topic = 'orders/cancelled';
        //                 $webhook->address = url('api/shopify/webhooks/orders/cancelled');
        //                 $webhook->format = 'json';
        //                 $webhook->success = 0;

        //                 if($e->getCode() == 422){
        //                     $webhook->success = 1;
        //                 }

        //                 $webhook->save();

        //                 report($e);
        //             }
        //         }
        //     }
        // })->everyMinute();

        // $schedule->call(function(){
        //     $suppliers = Suppliers::with('total_express_settings')->has('total_express_settings')->get();

        //     foreach($suppliers as $supplier){
        //         TotalExpressService::updateTrackings($supplier->total_express_settings);
        //     }
        // })->everyMinute();

        // $schedule->call(function(){
        //     //atualiza o valor do dólar (API)

        //     try {
        //         $return = json_decode(file_get_contents('http://economia.awesomeapi.com.br/json/usd'));
        //         if(isset($return[0])){
        //             if(isset($return[0]->code) && $return[0]->code == 'USD'){
        //                 $dolarPrice = $return[0]->ask;
        //                 DB::table('dollar')
        //                     ->where('id', 1)
        //                     ->update(['price' => number_format($dolarPrice, 2)]);

        //             }
        //         }
        //     } catch (\Exception $e) {
        //         Log::error($e);
        //     }

        // })->everyMinute();

        // //chinadivision somente s2m2
        // // $schedule->call(function(){
        // //     $china_service = new ChinaDivisionService();
        // //     $orders = SupplierOrders::where('status', 'paid')->where('supplier_id', 56)->where('exported_to_china_division', 0)->orderBy('id', 'desc')->get();

        // //     foreach($orders as $o){
        // //         $idOrder = $china_service->generateOrder($o);

        // //         if($idOrder){ //caso seja um código válido, salva
        // //             $shipping = SupplierOrderShippings::where('supplier_id', $o->supplier_id)
        // //                                             ->where('supplier_order_id', $o->id)
        // //                                             ->first();
        // //             if($shipping){
        // //                 //$shipping->status = 'sent';
        // //                 //$shipping->tracking_url = "https://www2.correios.com.br/sistemas/rastreamento/default.cfm/";
        // //                 //$shipping->tracking_number = $idOrder;
        // //                 //$shipping->save();

        // //                 $o->exported_to_china_division = 1;
        // //                 $o->save();

        // //                 //echo "código: ".$trackingNumberCorreios."<br>";
        // //             }
        // //         }
        // //     }
        // // })->everyMinute();

        // // $schedule->call(function(){
        // //     //cronjob para buscar os rastreios do chinadivision automaticamente para a s2m2
        // //     $supplier_orders_ids = SupplierOrders::where('supplier_id', 56)
        // //                         ->where('status', 'paid')
        // //                         ->whereHas('shipping', function ($query) {
        // //                             $query->whereNull('tracking_number');
        // //                         })
        // //                         ->pluck('id')
        // //                         ->toArray();

        // //     $china_service = new ChinaDivisionService();
        // //     $china_service->checkOrderTrackingNumberCronJob($supplier_orders_ids);

        // // })->everyMinute();

        // $schedule->call(function(){

        //     //cronjob cancelamento
        //     //seleciona todos os pedidos de cancelamento para o dia que ainda não tenham sido executados
        //     // $shopsCanceledPlans = ShopCanceledPlans::where('change_date', date("Y-m-d"))
        //     //                                     ->where('status', 'pending')
        //     //                                     ->orderBy('id', 'asc')
        //     //                                     ->limit(100)
        //     //                                     ->get();
        //     $shopsCanceledPlans = ShopCanceledPlans::where('status', 'pending')
        //                                         ->orderBy('id', 'asc')
        //                                         ->limit(100)
        //                                         ->get();

        //     // $shopsCanceledPlans = ShopCanceledPlans::where('status', 'pending')
        //     //                                     ->orderBy('id', 'asc')
        //     //                                     ->limit(100)
        //     //                                     ->get();

        //     foreach ($shopsCanceledPlans as $shopCanceledPlan) {
        //         $shop = Shops::find($shopCanceledPlan->shop_id);

        //         $safe2pay = new SafeToPayPlansService();

        //         //cancela a assinatura na safe2pay
        //         if($safe2pay->cancelShopSubscription($shop)){
        //             $shop->canceled_plan->status = 'executed';
        //             $shop->canceled_plan->save();
        //         }

        //         //muda o plano do usuário para o id do plano gratuito
        //         //$shop->contracted_plan->plan_id = 999; //magic number do plano gratuito

        //         // if($shop->contracted_plan->save()){
        //         //     //atualiza o shop canceled para executed
        //         //     $shop->canceled_plan->status = 'executed';
        //         //     $shop->canceled_plan->save();
        //         // }

        //     }
        // })->everyMinute();

        //job temporário pra buscar pedidos que eventualmente não tenham caido na mawa
        // $schedule->call(function(){
        //     $shops = DB::table('shops')
        //             ->leftJoin('shop_contracted_plans', 'shops.id', 'shop_contracted_plans.shop_id') //puxa as lojas com assinatura na safe
        //             ->leftJoin('internal_subscription_shops', 'shops.id', 'internal_subscription_shops.shop_id') //puxa as lojas com assinatura interna
        //             ->leftJoin('shopify_apps', 'shops.id', 'shopify_apps.shop_id') //puxa as lojas com assinatura interna
        //             ->where('shop_contracted_plans.subscription_status', 'Ativa')
        //             ->where('shopify_apps.app_key', '<>', '')
        //             ->where('shopify_apps.app_password', '<>', '')
        //             ->orWhere('internal_subscription_shops.status', 'active')
        //             ->orWhereIn('shops.id', \App\Http\Controllers\Shop\FunctionsController::getFreeShops()) //puxa as lojas que estão gratuitas
        //             ->select('shops.*')
        //             ->get('id');

        //     foreach ($shops as $shopDB) {
        //         //puxa as ordens manualmente de cada shop
        //         $shop = Shops::find($shopDB->id);

        //         if(isset($shop->shopify_app)){
        //             $result = ShopifyService::getPaidOrdersLimit($shop, 50); //puxa 50 ordens

        //             if($result && $result['status'] == 'success'){
        //                 //echo "Shop: ".$shop->name." - ordens: ".count($result['data']).'<br>';
        //                 foreach ($result['data'] as $shopify_order) {
        //                     ShopifyService::registerOrder($shop, $shopify_order);
        //                 }
        //             }
        //         }
        //     }

        // })->everyMinute();
    }




    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
