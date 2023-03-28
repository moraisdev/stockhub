<?php

namespace App\Services;

use App\Models\ErrorLogs;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Log;

use Auth;
use App\Models\Suppliers;
use App\Models\SupplierOrders;
use App\Models\SupplierOrderShippings;

class ChinaDivisionService{

    private function getApiKey($sup_order){
        return $sup_order && $sup_order->supplier && $sup_order->supplier->china_division_apikey ? $sup_order->supplier->china_division_apikey : NULL;
    }

    public function checkSendOrder($sup_order){
        if($sup_order->supplier->china_division_apikey != null && $sup_order->supplier->china_division_apikey != ''){
            return true;
        }else{
            return false;
        }
    }

    public function generateOrder($supplier_order){
        try {
            $posts = $this->generateJson($supplier_order);

            if(isset($posts->error) && $posts->error == true){                
                ErrorLogs::create(['status' => 500, 'message' => $posts['message'], 'file' => 'Erro no China Division']);
                Log::error('Custom error in ChinaDivisionService: '.$posts['message']);

                return false;
            }

            $url = 'https://api.chinadivision.com/order-create';

            $header = array(
                'Content-Type:application/x-www-form-urlencoded',
                'apikey: '.$this->getApiKey($supplier_order),
            );

            $retorno = json_decode($this->executeSendOrder($url, $header, $posts));

            if($retorno && isset($retorno->msg) && $retorno->msg == "success"){
                return $retorno->data->order_id;
            }

            return NULL;
        } catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            Log::error('Error in ChinaDivisionService', [$e]);
        }
    }

    private function generateJson($supplier_order){
        try {
            if(!isset($supplier_order->order->customer)){
                return (object)['error' => true, 'message' => 'Pedido sem cliente válido'];
            }

            if(!isset($supplier_order->order->customer->address)){
                return (object) ['error' => true, 'message' => 'Endereço do cliente inválido'];
            }

            if(count($supplier_order->items) < 1){
                return (object) ['error' => true, 'message' => 'Pedido sem itens cadastrados.'];
            }

            $arrItems = array();
            $supplier = Suppliers::where('id', $supplier_order->supplier_id)->first();

            $shippingMethod = '';

            foreach($supplier_order->items as $item){
                $order_item = OrderItems::where('product_variant_id', $item->variant->id)->where('order_id', $supplier_order->order_id)->first();

                //busca o shipping_method do último produto e atribui a nota toda
                $shippingMethod = ($order_item->variant->product->shipping_method_china_division ? $order_item->variant->product->shipping_method_china_division : 'CDEUB');

                $jsonItem = array(
                    'sku' => $item->variant->sku,
                    'product_name' => $item->variant->title,
                    'price' => number_format(($order_item && $order_item->external_price ? $order_item->external_price : 0), 2, '.',''),
                    'supplier' => $supplier->name,
                    'quantity' => $item->quantity,
                    'shipping_method' => $shippingMethod,
                );

                array_push($arrItems, $jsonItem);
            }

            // //transforma o cpf para o formato 000.000.000-00
            $val = strval($supplier_order->order->customer->address->company);
            $cpf = $val[0].$val[1].$val[2].".".$val[3].$val[4].$val[5].".".$val[6].$val[7].$val[8]."-".$val[9].$val[10];

            $post_data = array(
                //'order_id' => $supplier_order->id,
                'order_id' => $supplier_order->f_display_id,
                'first_name' => $supplier_order->order->customer->first_name,
                'last_name' => $supplier_order->order->customer->last_name,
                'ship_address1' => $supplier_order->order->customer->address->address1,
                'ship_address2' => $supplier_order->order->customer->address->address2,
                'ship_city' => $supplier_order->order->customer->address->city,
                'ship_state' => $supplier_order->order->customer->address->province_code,
                'ship_zip' => $supplier_order->order->customer->address->zipcode,
                'ship_country' => $supplier_order->order->customer->address->country,
                'ship_phone' => $supplier_order->order->customer->address->phone,
                'ship_email' => $supplier_order->order->customer->email,
                'vat' => $cpf,
                'quantity' => $this->countOrderItems($supplier_order->items),
                'info' => $arrItems,
                'shipping_method' => $shippingMethod,
                'inspection' => 0,
                'remark' => 'Pedido gerado pela '.config('app.name').'. ID:'.$supplier_order->display_id,
                'business_type' => 1
            );

            return $post_data;
        } catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            Log::error('Error in ChinaDivisionService', [$e]);
        }
    }

    private function countOrderItems($items){
        $count = 0;
        foreach ($items as $item) {
            $count += $item->quantity;
        }

        return $count;
    }

    private function executeSendOrder($url, $header, $data){
        try {
            $ch = curl_init();

            // Add apikey to header
            curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
            // Add data to bodyParam
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // HTTP request
            curl_setopt($ch , CURLOPT_URL , $url);
            $res = curl_exec($ch);
        
            return $res;
        }catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            Log::error('Error in ChinaDivisionService', [$e]);
        }
    }

    public function checkOrderTrackingNumber($ordersId){
        //percorre a lista de ids e verifica se tem o código de rastreio
        
        $codes = array();
        
        foreach($ordersId as $id){
            //carrega os dados da ordem
            $supplier = Auth::user();

            $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($id);

            //só faz caso não tenha sido exportada para o china division ainda
            if($supplier_order){
                $shipping = SupplierOrderShippings::where('supplier_id', $supplier_order->supplier_id)
                                                        ->where('supplier_order_id', $supplier_order->id)
                                                        ->first();


                //só atualiza caso ainda não o tenha feito
                if($shipping && !$shipping->tracking_number){
                    $url = 'https://api.chinadivision.com/order-info?order_id='.urlencode($supplier_order->f_display_id);

                    $header = array(
                        'Content-Type:application/x-www-form-urlencoded',
                        'apikey: '.$this->getApiKey($supplier_order),
                    );

                    $retorno = json_decode($this->executeSendOrder($url, $header, NULL));

                    if($retorno && isset($retorno->msg) && $retorno->msg == "success" && $retorno->data){

                        $trackingNumber = $retorno->data->tracking_number;

                        //atualiza os dados de envio
                        if($trackingNumber){ //caso seja um código válido, salva
                            $shipping->company = "CHINA POST";
                            $shipping->tracking_url = "http://www.stone3pl.com/";
                            $shipping->tracking_number = $trackingNumber;
                            $shipping->save();

                            $codes[$id] = $trackingNumber;
                        }
                    }
                }
            }
        }
        return $codes;
    }

    public function checkOrderTrackingNumberCronJob($ordersId){
        //cronjob para olhar somente as ordens da s2m2
        //percorre a lista de ids e verifica se tem o código de rastreio
        
        foreach($ordersId as $id){
            //carrega os dados da ordem
            $supplier_order = SupplierOrders::where('supplier_id', 56)->find($id);
            
            if($supplier_order){
                $shipping = SupplierOrderShippings::where('supplier_id', $supplier_order->supplier_id)
                                                        ->where('supplier_order_id', $supplier_order->id)
                                                        ->first();


                //só atualiza caso ainda não o tenha feito
                if($shipping && !$shipping->tracking_number){
                    $url = 'https://api.chinadivision.com/order-info?order_id='.urlencode($supplier_order->f_display_id);

                    $header = array(
                        'Content-Type:application/x-www-form-urlencoded',
                        'apikey: '.$this->getApiKey($supplier_order),
                    );

                    $retorno = json_decode($this->executeSendOrder($url, $header, NULL));
                    
                    if($retorno && isset($retorno->msg) && $retorno->msg == "success" && $retorno->data){

                        $trackingNumber = $retorno->data->tracking_number;

                        //atualiza os dados de envio
                        if($trackingNumber){ //caso seja um código válido, salva
                            $shipping->company = "CHINA POST";
                            $shipping->tracking_url = "http://www.stone3pl.com/";
                            $shipping->tracking_number = $trackingNumber;
                            $shipping->save();
                        }
                    }
                }
            }
        }
    }

}


?>
