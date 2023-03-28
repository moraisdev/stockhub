<?php
namespace App\Services;

use App\Models\SupplierOrders;
use App\Models\Suppliers;
use App\Models\TotalExpressSettings;
use Illuminate\Support\Facades\Log;

class TotalExpressService{
    protected $to_zipcode;
    protected $settings;

    protected $width = 0;
    protected $height = 0;
    protected $depth = 0;
    protected $weight = 0;
    protected $price = 0;

    public function __construct(TotalExpressSettings $settings){
        $this->settings = $settings;
    }

    public function setToZipcode($zipcode){
        $zipcode = $this->parseZipcode($zipcode);

        // Validates if it is a valid zipcode
        if(strlen($zipcode) != 8){
            return false;
        }

        $this->to_zipcode = $zipcode;
    }

    public function setPrice($price){
        $this->price = (float)$price;
    }

    /*
     * Product example:
     * [
     *      width,
     *      height,
     *      depth,
     *      weight,
     *      quantity
     * ]
     * */

    public function calcBoxSize(array $products){
        try {
            $total_weight = 0;
            $cubic_cm = 0;
            $total_price = 0;

            // Calculate total weight and cubic_cm of the shipping order
            foreach($products as $product){
                $total_weight += $product['qty'] * ($product['weight'] / 1000);
                $cubic_cm += $product['qty'] * $product['width'] * $product['height'] * $product['depth'];

                $total_price += $product['price'];
            }

            $cubic_root = round(pow($cubic_cm, 1/3), 2);

            // Check minimum values and set box size
            $this->width = $cubic_root < 11 ? 11 : $cubic_root;
            $this->height = $cubic_root < 2 ? 2 : $cubic_root;
            $this->depth = $cubic_root < 16 ? 16 : $cubic_root;
            $this->weight = $total_weight < 0.3 ? 0.3 : $total_weight;

            $this->setPrice($total_price);
        }catch(\Exception $e){
            Log::error('Error while setting shipping box size.', [$e]);

            return false;
        }

        return true;
    }

    public function getValorServico(){
        if($this->width == 0 || $this->height == 0 || $this->depth == 0 || $this->weight == 0 || !$this->to_zipcode){
            return false;
        }

        try {
            $client = new \SoapClient('https://edi.totalexpress.com.br/webservice_calculo_frete.php?wsdl', ['trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE, 'login' => $this->settings->login, 'password' => $this->settings->password]);

            $arguments= array('calcularFrete' => array(
                'TipoServico' => $this->settings->type,
                'CepDestino' => $this->to_zipcode,
                'Peso' => $this->weight,
                'ValorDeclarado' => $this->price,
                'TipoEntrega' => 0,
                'ServicoCOD' => false,
                'Altura' => $this->height,
                'Largura' => $this->width,
                'Profundidade' => $this->depth
            ));

            $options = array('location' => 'https://edi.totalexpress.com.br/webservice_calculo_frete.php');

            $result = $client->__soapCall('calcularFrete', $arguments, $options);

            if($result->DadosFrete){
                return str_replace(',','.', str_replace('.', '', $result->DadosFrete->ValorServico));
            }else{
                return false;
            }
        }catch(\Exception $e){
            Log::error('Shipping calc error.', [$e]);

            return false;
        }

        return true;
    }

    protected function parseZipcode($zipcode){
        return preg_replace('/\D/', '', $zipcode);
    }

    public static function prepareOrderProducts($order_items){
        $result = [];

        foreach($order_items as $order_items){
            $result[] = [
                'width' => $order_items->variant->width,
                'height' => $order_items->variant->height,
                'depth' => $order_items->variant->depth,
                'weight' => $order_items->variant->weight_in_grams,
                'qty' => $order_items->quantity,
                'price' => $order_items->amount
            ];
        }

        return $result;
    }

    //total_weight = peso total
    //product_type = natureza do produto com maior quantidade da remessa
    //$icms = 1 ou 0 pra caso e isento ou nao
    public static function exportOrder($supplier_order, $nfe = null){
        try {
            $settings = $supplier_order->supplier->total_express_settings;

            if(!$supplier_order->order->customer || !$supplier_order->order->customer->address || !$settings){
                return false;
            }

            $total_weight = 0;
            $highest_quantity = 0;
            $product_type = '';
            $icms_exemption = 1;

            foreach($supplier_order->items as $item){
                $total_weight += ($item->variant->weight_in_grams * $item->quantity) / 1000;

                if($item->quantity > $highest_quantity){
                    $product_type = $item->variant->title;

                    if($item->variant->product->icms_exemption == 0){
                        $icms_exemption = 0;
                    }
                }
            }

            $order_id = str_pad($supplier_order->display_id, 8, "0", STR_PAD_LEFT);

            $data = [
                'CodRemessa' => $order_id,
                'Encomendas' => [
                    'Encomenda' => [
                        'TipoServico' => 1,
                        'TipoEntrega' => 0,
                        'Peso' => $total_weight,
                        'Volumes' => 1,
                        'CondFrete' => 'CIF',
                        'Pedido' => $order_id,
                        'Natureza' => $product_type,
                        'IsencaoIcms' => $icms_exemption,
                        'DestNome' => $supplier_order->order->customer->address->name,
                        'DestCpfCnpj' => $supplier_order->order->customer->address->company,
                        'DestEnd' => $supplier_order->order->customer->address->address1,
                        'DestEndNum' => '#',
                        'DestBairro' => $supplier_order->order->customer->address->address2,
                        'DestCidade' => $supplier_order->order->customer->address->city,
                        'DestEstado' => $supplier_order->order->customer->address->province_code,
                        'DestCep' => (int)$supplier_order->order->customer->address->zipcode,
                        'DestEmail' => $supplier_order->order->customer->email,
                        'DestTelefone1' => (int)preg_replace('/\D/', '', $supplier_order->order->customer->address->phone)
                    ],
                ]
            ];

            if($nfe){
                $xml = simplexml_load_file($nfe->getPathName());

                $data['Encomendas']['Encomenda']['DocFiscalNFe'] = [
                    [
                        'NfeNumero' => (int)$xml->NFe->infNFe->ide->nNF,
                        'NfeSerie' => (int)$xml->NFe->infNFe->ide->serie,
                        'NfeData' => date('Y-m-d', strtotime((string)$xml->NFe->infNFe->ide->dhEmi)),
                        'NfeValTotal' => (string)$xml->NFe->infNFe->total->ICMSTot->vNF,
                        'NfeValProd' => (string)$xml->NFe->infNFe->total->ICMSTot->vProd,
                        'NfeCfop' => null,
                        'NfeChave' => str_replace('NFe', '', (string)$xml->NFe->infNFe['Id']),
                    ]
                ];
            }else{
                $data['Encomendas']['Encomenda']['DocFiscalO'] = [
                    [
                        'NfoTipo' => '00',
                        'NfoDescricao' => null,
                        'NfoNumero' => str_pad($supplier_order->id, 9, "0", STR_PAD_LEFT),
                        'NfoData' => date('Y-m-d', strtotime($supplier_order->created_at)),
                        'NfoValTotal' => number_format($supplier_order->amount, 2),
                        'NfoValProd' => number_format($supplier_order->total_amount, 2),
                        'NfoCfop' => null
                    ]
                ];
            }

            // TEST LOGIN: direto-qa
            // TEST PASSWORD: nLmFt6k7

            $client = new \SoapClient('https://edi.totalexpress.com.br/webservice24.php?wsdl', ['trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE, 'login' => $settings->login, 'password' => $settings->password]);
            $arguments= array('RegistraColeta' => $data);

            $options = array('location' => 'https://edi.totalexpress.com.br/webservice24.php');

            $result = $client->__soapCall('RegistraColeta', $arguments, $options);

            if($result->CodigoProc == 1){
                return true;
            }else{
                Log::error('ERRO NO ENVIO DE REMESSA.', [$result]);
                return false;
            }
        }catch(\Exception $e){
            report($e);

            return false;
        }
    }

    public static function updateTrackings($settings){
        $client = new \SoapClient('https://edi.totalexpress.com.br/webservice24.php?wsdl', ['trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE, 'login' => $settings->login, 'password' => $settings->password]);
        $arguments= array('ObterTracking' => ['DataConsulta' => date('Y-m-d')]);

        $options = array('location' => 'https://edi.totalexpress.com.br/webservice24.php');

        $result = $client->__soapCall('ObterTracking', $arguments, $options);

        try{
            if(isset($result->ArrayLoteRetorno)){
                foreach($result->ArrayLoteRetorno as $lote){
                    if(isset($lote->LoteRetorno->ArrayEncomendaRetorno)){
                        foreach($lote->LoteRetorno->ArrayEncomendaRetorno as $encomenda){
                            $order_id = (int)$encomenda->EncomendaRetorno->Pedido;

                            $supplier_order = SupplierOrders::find($order_id);

                            if(isset($encomenda->ArrayStatusTotal)){
                                foreach($encomenda->ArrayStatusTotal as $total){
                                    $codigo = $total->StatusTotal->CodStatus;

                                    if($codigo == 1){
                                        $supplier_order->shipping->status = 'sent';
                                        $supplier_order->shipping->company = 'Total Express';
                                        $supplier_order->shipping->tracking_url = 'http://tracking.totalexpress.com.br/tracking/0';
                                        $supplier_order->shipping->tracking_number = $encomenda->AWB;

                                        $supplier_order->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch(\Exception $e){
            report($e);
        }
    }
}
