<?php

namespace App\Services;

use App\Models\ErrorLogs;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Log;

use Auth;
use App\Models\SupplierOrders;

use App\Models\SupplierOrderShippings;

class BlingService{

    private function getApiKey($sup_order){
        return $sup_order && $sup_order->supplier && $sup_order->supplier->bling_apikey ? $sup_order->supplier->bling_apikey : NULL;
    }

    public function checkSendOrder($sup_order){
        if($sup_order->supplier->bling_apikey != null && $sup_order->supplier->bling_apikey != ''){
            return true;
        }else{
            return false;
        }
    }

    public function importProducts($supplier, $page){
       try {


            //carrega todos os produtos do fornecedor no bling
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', "https://bling.com.br/Api/v2/produtos/page=".$page."/json/?apikey=".$supplier->bling_apikey.'&estoque=S&imagem=S');

            $response = json_decode($response->getBody())->retorno->produtos;
            return $response;

        } catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            Log::error('Error in BlingService', [$e]);
            return false;
        }
    }

    public function importProductsid($supplier, $product){
        try {
 
 
             //carrega todos os produtos do fornecedor no bling
             $client = new \GuzzleHttp\Client();
             $response = $client->request('GET', "https://bling.com.br/Api/v2/produto/".$product."/json/?apikey=".$supplier->bling_apikey.'&estoque=S&imagem=S');
 
             $response = json_decode($response->getBody())->retorno->produtos;
             return $response;
 
         } catch (\Exception $e){
             ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
             Log::error('Error in BlingService', [$e]);
             return false;
         }
     }

    public function generateOrder($supplier_order){
        try {
            $get_xml = $this->generateXml($supplier_order);

            if(isset($get_xml['error']) && $get_xml['error'] == true){
                ErrorLogs::create(['status' => 500, 'message' => $get_xml['message'], 'file' => 'Erro no BLING']);
                Log::error('Custom error in BlingService: '.$get_xml['message']);

                return false;
            }

            $url = 'https://bling.com.br/Api/v2/pedido/json/';
            $xml = $get_xml;
            $posts = array (
                "apikey" => $this->getApiKey($supplier_order),
                "xml" => rawurlencode($xml)
            );

            $retorno = json_decode($this->executeSendOrder($url, $posts));

            if($retorno && isset($retorno->retorno->pedidos[0]) && isset($retorno->retorno->pedidos[0]->pedido->codigos_rastreamento->codigo_rastreamento)){
                return $retorno->retorno->pedidos[0]->pedido->codigos_rastreamento->codigo_rastreamento; //retorna o código de rastreio ou nulo caso dê errado
            }

            return NULL;
        } catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            Log::error('Error in BlingService', [$e]);
        }
    }

    private function generateXml($supplier_order){
        try {
            if(!isset($supplier_order->order->customer)){
                return ['error' => true, 'message' => 'Pedido sem cliente válido'];
            }

            if(!isset($supplier_order->order->customer->address)){
                return ['error' => true, 'message' => 'Endereço do cliente inválido'];
            }

            if(count($supplier_order->items) < 1){
                return ['error' => true, 'message' => 'Pedido sem itens cadastrados.'];
            }

            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<pedido>';
            $xml .= '<numero>'.$supplier_order->id.'</numero>';
            $xml .= '<cliente>';
                $xml .= '<nome>'.$supplier_order->order->customer->first_name.' '.$supplier_order->order->customer->last_name.'</nome>';
                if($supplier_order->order->customer->address->company != null){
                    $xml .= '<cpf_cnpj>'.$supplier_order->order->customer->address->company.'</cpf_cnpj>';
                }
                $xml .= '<tipoPessoa>F</tipoPessoa>';
                $xml .= '<endereco>'.$supplier_order->order->customer->address->address1.'</endereco>';
                $xml .= '<bairro>'.$supplier_order->order->customer->address->address2.'</bairro>';
                $xml .= '<cep>'.$supplier_order->order->customer->address->zipcode.'</cep>';
                $xml .= '<cidade>'.$supplier_order->order->customer->address->city.'</cidade>';
                $xml .= '<uf>'.$supplier_order->order->customer->address->province_code.'</uf>';
                $xml .= '<fone>'.$supplier_order->order->customer->address->phone.'</fone>';
                $xml .= '<email>'.$supplier_order->order->customer->email.'</email>';
            $xml .= '</cliente>';

            if($supplier_order->supplier->correios_settings){
                $xml .= "<transporte>";
                    $xml .= "<transportadora></transportadora>";
                    $xml .= "<tipo_frete>R</tipo_frete>";
                    $xml .= "<servico_correios>".$supplier_order->supplier->correios_settings->correios_services_bling."</servico_correios>";
                    $xml .= "<dados_etiqueta>";
                        $xml .= "<nome>Endereço de entrega</nome>";
                        $xml .= "<endereco>".$supplier_order->order->customer->address->address1."</endereco>";
                        $xml .= "<bairro>".$supplier_order->order->customer->address->address2."</bairro>";
                        $xml .= "<cep>".$supplier_order->order->customer->address->zipcode."</cep>";
                        $xml .= "<municipio>".$supplier_order->order->customer->address->city."</municipio>";
                        $xml .= "<uf>".$supplier_order->order->customer->address->province_code."</uf>";
                        $xml .= "<fone>".$supplier_order->order->customer->address->phone."</fone>";
                        $xml .= "<email>".$supplier_order->order->customer->email."</email>";
                    $xml .= "</dados_etiqueta>";
                    $xml .= "<volumes>";
                        $xml .= "<volume>";
                            $xml .= "<servico>".$supplier_order->supplier->correios_settings->correios_services_bling."</servico>";
                            $xml .= "<codigoRastreamento></codigoRastreamento>";
                        $xml .= "</volume>";
                    $xml .= "</volumes>";
                $xml .= "</transporte>";
            }
             $supplier_order_itens =  OrderItems::where('order_id', $supplier_order->id)->get();
            $xml .= '<itens>';
            foreach($supplier_order_itens as $item){
             //   $order_item = OrderItems::where('product_variant_id', $item->variant->id)->where('order_id', $supplier_order->order_id)->first();
                $xml .= '<item>';
                $xml .= '<codigo>'.$item->sku.'</codigo>';
                $xml .= '<descricao>'.$item->title.'</descricao>';
                $xml .= '<un>Pç</un>';
                $xml .= '<qtde>'.$item->quantity.'</qtde>';
                $xml .= '<vlr_unit>'.number_format(($item->external_price), 2, '.','').'</vlr_unit>';
                $xml .= '</item>';
            }
            
            
            $xml .= '</itens>';
            
               $xml .= "<parcelas>";
            $xml .= "<parcela>";
            $xml .= "<data>".date('d-m-Y', strtotime($supplier_order->created_at))."</data>";
            $xml .= "<vlr>".$supplier_order->total_amount."</vlr>";
            $xml .= "<obs>Parcela</obs>";
            $xml .= "</parcela>";
            $xml .= "</parcelas>";
            
            
            $xml .= '<obs_internas>Pedido gerado pela '.config('app.name').'. ID:'.$supplier_order->display_id.'</obs_internas>';
            $xml .= '</pedido>';

            return $xml;

        } catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            Log::error('Error in BlingService', [$e]);
        }
    }

    private function executeSendOrder($url, $data){
        try {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_POST, count($data));
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($curl_handle);
            curl_close($curl_handle);

            return $response;
        }catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            Log::error('Error in BlingService', [$e]);
        }
    }

    public function checkOrderTrackingNumber($ordersId){
        //percorre a lista de ids e verifica se tem o código de rastreio
        $codes = array();
        foreach($ordersId as $id){
            //carrega os dados da ordem
            $supplier = Auth::user();

            $supplier_order = SupplierOrders::where('supplier_id', $supplier->id)->find($id);

            //só faz caso não tenha sido exportada para o bling ainda
            if($supplier_order){
                $shipping = SupplierOrderShippings::where('supplier_id', $supplier_order->supplier_id)
                                                        ->where('supplier_order_id', $supplier_order->id)
                                                        ->first();


                //só atualiza caso ainda não o tenha feito
                if($shipping && !$shipping->tracking_url && !$shipping->tracking_number){
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('GET', "https://bling.com.br/Api/v2/pedido/".$id."/json/?apikey=".$this->getApiKey($supplier_order));

                    $response = json_decode($response->getBody());

                    if($response && isset($response->retorno->pedidos[0]) && isset($response->retorno->pedidos[0]->pedido->codigosRastreamento->codigoRastreamento)){
                        $trackingNumberCorreios = $response->retorno->pedidos[0]->pedido->codigosRastreamento->codigoRastreamento;

                        //atualiza os dados de envio
                        if($trackingNumberCorreios){ //caso seja um código válido, salva
                            $shipping->tracking_url = "https://www2.correios.com.br/sistemas/rastreamento/default.cfm/";
                            $shipping->tracking_number = $trackingNumberCorreios;
                            $shipping->save();

                            $codes[$id] = $trackingNumberCorreios;
                            
                        }
                    }
                }
            }
        }

        return $codes;
    }



    private function confirmapedidoXml($supplier_order){
        try {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<pedido>';
     //   $xml .= '<idSituacao>6</idSituacao>';
        $xml .= '<situacao>1</situacao>';
        $xml .= '</pedido>';
        return $xml;

    } catch (\Exception $e){
        ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        Log::error('Error in BlingService', [$e]);
    }

    }

    public function atualizarpedido($supplier_order , $supplier){
        try {
            $get_xml_p = $this->confirmapedidoxml($supplier_order);

            if(isset($get_xml_p['error']) && $get_xml_p['error'] == true){
                ErrorLogs::create(['status' => 500, 'message' => $get_xml_p['message'], 'file' => 'Erro no BLING']);
                Log::error('Custom error in BlingService: '.$get_xml_p['message']);

                return false;
            }

            $url = "https://bling.com.br/Api/v2/pedido/".$supplier_order."/json";
            $xml = $get_xml_p;
            $posts = array (
                "apikey" => $supplier->bling_apikey,
                "xml" =>  rawurlencode($xml)
            );

            $retorno = json_decode($this->executeSendPedido($url, $posts));


                return $retorno; //retorna


           // return NULL;
        } catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            Log::error('Error in BlingService', [$e]);
        }
    }

    private function executeSendPedido($url, $data){
        try {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl_handle, CURLOPT_POST, count($data));
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($curl_handle);
            curl_close($curl_handle);

            return $response;
        }catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            Log::error('Error in BlingService', [$e]);
        }
    }




public function exportProducts($shop , $prod ,  $stock ,  $imagens)
{

    try {

        $get_xml_exp = $this->exportXml($prod ,  $stock ,  $imagens);

        if(isset($get_xml_p['error']) && $get_xml_p['error'] == true){
            ErrorLogs::create(['status' => 500, 'message' => $get_xml_p['message'], 'file' => 'Erro no BLING']);
            Log::error('Custom error in BlingService: '.$get_xml_p['message']);

            return false;
        }

        $url = "https://bling.com.br/Api/v2/produto/json/";
        $xml = $get_xml_exp;
        $posts = array (
            "apikey" => $shop->bling_apikey,
            "xml" =>  rawurlencode($xml)
        );

        $retorno = json_decode($this->executeInsertProduct($url, $posts));


            return $retorno; //retorna


       // return NULL;
    } catch (\Exception $e){
        ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        Log::error('Error in BlingService', [$e]);
    }
}


private function exportXml($prod , $stock , $imagens){
    try {
        
        $peso = $prod->weight_in_grams /  1000;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<produto>';
        $xml .= '<codigo>'.$prod->sku.'</codigo>';
        $xml .= '<descricao>'.$prod->title.'</descricao>';
        $xml .= '<situacao>Ativo</situacao>';
        $xml .= '<descricaoCurta>'.$prod->description.'</descricaoCurta>';
        $xml .= '<descricaoComplementar></descricaoComplementar>';
        $xml .= '<un>UN</un>';
        $xml .= '<vlr_unit>0.00</vlr_unit>';
        $xml .= '<preco_custo>'.$prod->price.'</preco_custo>';
        $xml .= '<peso_bruto>'.$peso.'</peso_bruto>';
        $xml .= '<peso_liq>'.$peso.'</peso_liq>';
        $xml .= '<class_fiscal>'.$prod->ncm.'</class_fiscal>';
        $xml .= '<marca></marca>';
        $xml .= '<origem>0</origem>';
        $xml .= '<gtin>'.$prod->ean_gtin.'</gtin>';
        $xml .= '<estoque>'.$stock->quantity.'</estoque>';
        $xml .= '<largura>'.$prod->width.'</largura>';
        $xml .= '<altura>'.$prod->height.'</altura>';
        $xml .= '<profundidade>'.$prod->depth.'</profundidade>';
        $xml .= '<estoqueMinimo></estoqueMinimo>';
        $xml .= '<estoqueMaximo></estoqueMaximo>';
        $xml .= '<condicao>Novo</condicao>';
        $xml .= '<freteGratis>N</freteGratis>';
        $xml .= '<linkExterno></linkExterno>';
        $xml .= '<observacoes>Produto Exportado pelo '. env('APP_NAME').'</observacoes>';
        $xml .= '<unidadeMedida>Un</unidadeMedida>';
       $xml .= '<volumes>1</volumes>';
       $xml .= '<imagens>';
       foreach($imagens as $img){
              $xml .= ' <url>'.$img->src.'</url>';
              }
        $xml .= '</imagens>';

        $xml .= '</produto>';

        return $xml;

    } catch (\Exception $e){
        ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        Log::error('Error in BlingService', [$e]);
    }
}

function executeInsertProduct($url, $data){
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_POST, count($data));
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($curl_handle);
    curl_close($curl_handle);
    return $response;
}



public function importProduto($supplier, $resp){

    try {


        //carrega todos os produtos do fornecedor no bling
        $client = new \GuzzleHttp\Client();
       $url = $client->request('GET', "https://bling.com.br/Api/v2/produto/".$resp->sku."/json/&apikey=".$supplier->bling_apikey.'&estoque=S&imagem=S');

       $response = json_decode($url->getBody())->retorno->produtos;
       return $response;


} catch (\Exception $e){
    ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
    Log::error('Error in BlingService', [$e]);
    return false;
}

}




}


?>
