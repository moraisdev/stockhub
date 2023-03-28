<?php

namespace App\Http\Controllers\Admin;



use App\Models\SupplierOrderGroup;
use App\Services\CurrencyService;
use App\Services\ExportService;
use App\Services\Shop\OrdersService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\SupplierOrders;
use App\Mail\ApprovedRegistration;
use Mail;

//retirar depois
use App\Services\BlingService;
use App\Services\MelhorEnvioService;
use App\Models\Shops;
use App\Models\FreteMelhorEnvio;
use App\Models\SupplierOrderShippings;

class DashboardController extends Controller
{
    public function index(){
        // $idsBugados = [4016,3347,3334];

        // //EXCEÇÃO
        // $supplierOrders = SupplierOrders::whereIn('id', $idsBugados)->get();
        // //dd($supplierOrders);
        // foreach ($supplierOrders as $s) {
        //     \App\Http\Controllers\Supplier\OrdersController::updateAutomaticShippingMelhorEnvio($s->id, $s->supplier);
        // }
        // //FIM EXCEÇÃO

        // //cronjob para buscar todos os fretes na melhor envio e verificar se já foi enviado e tem etiqueta e link
        // $fretesMelhorEnvio = FreteMelhorEnvio::whereIn('supplier_order_id', $idsBugados)
        //                                     ->get();
        // dd($fretesMelhorEnvio);

        // $melhorEnvioService = new MelhorEnvioService();

        // foreach ($fretesMelhorEnvio as $freteMelhorEnvio) {
        //     // $responseFrete = $melhorEnvioService->updateStatusFreight($freteMelhorEnvio->melhor_envio_id);
            
        //     // if($responseFrete && isset($responseFrete->status)){
        //         // $freteMelhorEnvio->status = $responseFrete->status;                

        //         // $client = new \GuzzleHttp\Client();
                    
        //         // if(!$responseFrete->tracking){//gera a etiqueta
        //         //     $responseTag = $melhorEnvioService->generateTag($freteMelhorEnvio->melhor_envio_id);

        //         //     if($responseTag){ //caso tenha gerado a etiqueta com sucesso
        //         //         //pega o número de rastreio e pega o link da etiqueta
        //         //         $responseFrete = $melhorEnvioService->updateStatusFreight($freteMelhorEnvio->melhor_envio_id);
        //         //         if($responseFrete && $responseFrete->tracking){
        //         //             $freteMelhorEnvio->tracking = $responseFrete->tracking;        
        //         //         }
        //         //     }
        //         // }else{ //caso ja tenha gerado a etiqueta, só pega o número do rastreio
        //         //     $freteMelhorEnvio->tracking = $responseFrete->tracking;
        //         // }

        //         // //imprime a etiqueta
        //         // $linkTag = $melhorEnvioService->printTag($freteMelhorEnvio->melhor_envio_id);
        //         // if($linkTag){
        //         //     $freteMelhorEnvio->tag_url = $linkTag;
        //         // }

        //         //if($freteMelhorEnvio->save()){
        //             //já aproveita e atualiza o código de rastreio oficial
        //             $freteMelhorEnvio->supplier_order->shipping->tracking_number = $freteMelhorEnvio->tracking;
        //             //$freteMelhorEnvio->supplier_order->shipping->company = $responseFrete->service->name;
        //             $freteMelhorEnvio->supplier_order->shipping->tracking_url = 'https://www.melhorrastreio.com.br/rastreio/'.$freteMelhorEnvio->tracking;
        //             if($freteMelhorEnvio->supplier_order->shipping->save()){
        //                 //atualiza o status do frete automaticamente na shopify e cartx
        //                 if($freteMelhorEnvio->status == 'posted'){
                            
        //                     $freteMelhorEnvio->supplier_order->shipping->status = 'sent';

        //                     if($freteMelhorEnvio->supplier_order->shipping->save()){
        //                         \App\Http\Controllers\Supplier\OrdersController::updateAutomaticShippingMelhorEnvio($freteMelhorEnvio->supplier_order->id, $freteMelhorEnvio->supplier_order->supplier);
        //                     }
        //                 }
        //             }
        //         //}
        //     //}
        // }


        //pega todas as supplier_orders que estão como pagas e não tem o frete na melhor envio
        // $orders = SupplierOrders::where('created_at', '>', '2021-03-26 00:00:00')
        //                         ->where('status', 'paid')
        //                         ->get();
        // $arrOrdersPendingMelhor = array();
        // foreach ($orders as $order) {
        //     if($order->supplier->shipping_method == 'melhor_envio'){
        //         $freteMelhor = FreteMelhorEnvio::where('supplier_order_id', $order->id)
        //                                     ->first();
        //         if(!$freteMelhor){
        //             array_push($arrOrdersPendingMelhor, $order);
        //         }
        //     }
            
        // }
        
        // //dd(count($arrOrdersPendingMelhor));
        // $arrErros = array();
        // for ($i=0; $i < count($arrOrdersPendingMelhor); $i++) {
        //     $s = $arrOrdersPendingMelhor[$i];
        //     //tenta adicionar novamente ao carrinho as ordens que não foram pra melhor
        //     //como a ordem foi de fato paga, realiza a compra do frete na melhor envio (adiciona no carrinho)
        //     $melhorEnvioService = new MelhorEnvioService();
        //     $melhorEnvioService->setFromZipcode($s->supplier->address->zipcode);
        //     $melhorEnvioService->setToZipcode($s->order->customer->address->zipcode);
        //     $melhorEnvioService->prepareOrderProductsSupplier($s->items);
        //     $responseMelhorEnvio = $melhorEnvioService->quoteBuyFreight($s->supplier, $s->order->shop, $s->order->customer, $s->order);

        //     //salva o id vindo da melhor envio
        //     if($s->supplier->shipping_method == 'melhor_envio' && $responseMelhorEnvio && $responseMelhorEnvio->freteId != ''){
        //         $freteMelhorEnvio = FreteMelhorEnvio::firstOrCreate([
        //             'order_id' => $s->order->id,
        //             'supplier_id' => $s->supplier->id,
        //             'supplier_order_id' => $s->id
        //         ]);
                
        //         $freteMelhorEnvio->amount = $responseMelhorEnvio->valor; //valor do frete
        //         $freteMelhorEnvio->service_id = $responseMelhorEnvio->serviceId; //id to tipo de serviço 1 - PAC, 2 - SEDEX, 3 - Mini Envios
        //         $freteMelhorEnvio->status = $responseMelhorEnvio->status;                    
        //         $freteMelhorEnvio->melhor_envio_id = $responseMelhorEnvio->freteId; //id do frete adicionado ao carrinho da melhor envio
        //         $freteMelhorEnvio->protocol = $responseMelhorEnvio->protocol; //salva o protocolo
        //         $freteMelhorEnvio->save();
        //     }else{
        //         array_push($arrErros, $s);
        //     }
        // }
        // dd($arrErros);
        

        // $bling_service = new BlingService();
        // $orders = SupplierOrders::where('status', 'paid')->where('exported_to_bling', 0)->get();

        // foreach($orders as $o){
        //     if($bling_service->checkSendOrder($o)){
        //         $trackingNumberCorreios = $bling_service->generateOrder($o);

        //         if($trackingNumberCorreios){ //caso seja um código válido, salva
        //             $shipping = SupplierOrderShippings::where('supplier_id', $o->supplier_id)
        //                                             ->where('supplier_order_id', $o->id)
        //                                             ->first();
        //             // if($shipping){
        //             //     //$shipping->status = 'sent';
        //             //     $shipping->tracking_url = "https://www2.correios.com.br/sistemas/rastreamento/default.cfm/";
        //             //     $shipping->tracking_number = $trackingNumberCorreios;
        //             //     $shipping->save();



        //             //     //echo "código: ".$trackingNumberCorreios."<br>";
        //             // }

        //             $o->exported_to_bling = 1;
        //             $o->save();
        //         }
        //     }
        // }
        // $shopsMail = Shops::whereIn('id', [480, 481, 349])->orderBy('id', 'desc')->get('email');
        // //dd($shopsMail);
        // foreach ($shopsMail as $shop) {
        //     Mail::to($shop->email)->send(new ApprovedRegistration($shop->email));
        // }

        $totalAmount = SupplierOrders::where("status", "paid")->sum("total_amount");
        $totalAmountWeek = SupplierOrders::where("created_at",">", Carbon::now()->subDays(7))->where("status", "paid")->sum("total_amount");
        $totalAmountMedia = SupplierOrders::where("created_at",">", Carbon::now()->subMonths(3))->where("status", "paid")->sum("total_amount")/3;
       
        return view('admin.dashboard.index', compact('totalAmount','totalAmountWeek','totalAmountMedia'));
    }

    public function test(){
        $service = new ExportService();
        $file = $service->ordersToExcel([289, 290, 291]);

        return $file;
    }
}
