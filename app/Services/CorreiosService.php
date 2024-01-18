<?php
namespace App\Services;

use App\Models\CorreiosContracts;
use App\Models\Suppliers;
use Illuminate\Support\Facades\Log;

class CorreiosService{
    protected $from_zipcode, $to_zipcode;

    protected $width = 0;
    protected $height = 0;
    protected $depth = 0;
    protected $weight = 0;

    public function setFromZipcode($zipcode){
        $zipcode = $this->parseZipcode($zipcode);

        // Validates if it is a valid zipcode
        if(strlen($zipcode) != 8){
            return false;
        }

        $this->from_zipcode = $zipcode;
    }

    public function setToZipcode($zipcode){
        $zipcode = $this->parseZipcode($zipcode);

        // Validates if it is a valid zipcode
        if(strlen($zipcode) != 8){
            return false;
        }

        $this->to_zipcode = $zipcode;
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

            // Calculate total weight and cubic_cm of the shipping order
            foreach($products as $product){
                $total_weight += $product['qty'] * ($product['weight'] / 1000);
                $cubic_cm += $product['qty'] * $product['width'] * $product['height'] * $product['depth'];
            }

            $cubic_root = round(pow($cubic_cm, 1/3), 2);

            // Check minimum values and set box size
            $this->width = $cubic_root < 11 ? 11 : $cubic_root;
            $this->height = $cubic_root < 2 ? 2 : $cubic_root;
            $this->depth = $cubic_root < 16 ? 16 : $cubic_root;
            $this->weight = $total_weight < 0.3 ? 0.3 : $total_weight;
        }catch(\Exception $e){
            Log::error('Error while setting shipping box size.', [$e]);

            return false;
        }

        return true;
    }

    public function getShippingPrices($supplier_id=null){
        if($this->width == 0 || $this->height == 0 || $this->depth == 0 || $this->weight == 0 || !$this->from_zipcode || !$this->to_zipcode){
            return false;
        }

        return (object)[
            'pac' => $this->calcPAC($supplier_id),
            'sedex' => $this->calcSEDEX(),
        ];
    }

    protected function parseZipcode($zipcode){
        return preg_replace('/\D/', '', $zipcode);
    }

    protected function calcPAC($supplier_id=null){
        try {
            $correios_contract = CorreiosContracts::where('supplier_id', $supplier_id)->where('active', 1)->where('service_code', '!=', null)->first();
            if($correios_contract){
                $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa='.$correios_contract->sigep_user.'&sDsSenha='.$correios_contract->sigep_password.'&sCepOrigem='. $this->from_zipcode .'&sCepDestino='. $this->to_zipcode .'&nVlPeso='. $this->weight .'&nCdFormato=1&nVlComprimento='.$this->depth.'&nVlAltura='.$this->height.'&nVlLargura='.$this->width.'&sCdMaoPropria=N&nVlValorDeclarado=0&sCdAvisoRecebimento=N&nCdServico='.$correios_contract->service_code.'&nVlDiametro=0&StrRetorno=xml';;
            }else{
                $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem='. $this->from_zipcode .'&sCepDestino='. $this->to_zipcode .'&nVlPeso='. $this->weight .'&nCdFormato=1&nVlComprimento='.$this->depth.'&nVlAltura='.$this->height.'&nVlLargura='.$this->width.'&sCdMaoPropria=N&nVlValorDeclarado=0&sCdAvisoRecebimento=N&nCdServico=04510&nVlDiametro=0&StrRetorno=xml';;
            }
            $shippingResult = simplexml_load_string(file_get_contents($url));

            return str_replace(',','.',$shippingResult->cServico->Valor);
        }catch(\Exception $e){
            Log::error('Shipping calc error.', [$e]);

            return false;
        }

        return true;
    }

    protected function calcSEDEX(){
        try{
            $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem='. $this->from_zipcode .'&sCepDestino='. $this->to_zipcode .'&nVlPeso='. $this->weight .'&nCdFormato=1&nVlComprimento='.$this->depth.'&nVlAltura='.$this->height.'&nVlLargura='.$this->width.'&sCdMaoPropria=N&nVlValorDeclarado=0&sCdAvisoRecebimento=N&nCdServico=04014&nVlDiametro=0&StrRetorno=xml';;
            $shippingResult = simplexml_load_string(file_get_contents($url));

            return str_replace(',','.',$shippingResult->cServico->Valor);
        }catch(\Exception $e){
            Log::error('Shipping calc error.', [$e]);

            return false;
        }

        return true;
    }

    public static function prepareOrderProducts($order_items){
        $result = [];

        foreach($order_items as $order_items){
            $result[] = [
                'width' => $order_items->variant->width,
                'height' => $order_items->variant->height,
                'depth' => $order_items->variant->depth,
                'weight' => $order_items->variant->weight_in_grams,
                'qty' => $order_items->quantity
            ];
        }

        return $result;
    }
}
