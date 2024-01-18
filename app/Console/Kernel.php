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
