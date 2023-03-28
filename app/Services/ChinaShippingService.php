<?php
namespace App\Services;

use App\Models\Suppliers;
//use App\Models\TotalExpressSettings;
use Illuminate\Support\Facades\Log;

class ChinaShippingService{
    protected $to_zipcode;

    protected $weight = 0;
    protected $max_packing_weight = 0;

    protected $CDEUB = []; //valores desse tipo de frete
    protected $PUAM = []; //valores desse tipo de frete
    protected $SZEUB = []; //valores desse tipo de frete
    

    protected $shippingMethod = '';

    public function __construct(){
        //o valor total desse frete é a soma desses dois valores
        //Preço do frete + taxa de manuseio
        $dataCsv = [];
        if (($file = fopen(public_path().'/assets/static/FRETE CHINA - MAWA.csv',"r")) !== FALSE){

            while (($data = fgetcsv($file, 0, ';')) !== FALSE){ //le uma linha do csv
                array_push($dataCsv, $data);
            }
            fclose($file);
        }

        //percorre o array dos dados vindos do csv e adiciona nos respectivos vetores
        for($i = 1; $i < count($dataCsv); $i++){
            array_push($this->CDEUB, ['shipping_price' => (float)str_replace("$","",str_replace(",",".",$dataCsv[$i][2])), 'handling_fee' => (float)str_replace("$","",str_replace(",",".",$dataCsv[$i][3]))]);
            array_push($this->PUAM, ['shipping_price' => (float)str_replace("$","",str_replace(",",".",$dataCsv[$i][6])), 'handling_fee' => (float)str_replace("$","",str_replace(",",".",$dataCsv[$i][7]))]);
        }

        //Lê o segundo csv
        $dataCsv2 = [];
        if (($file2 = fopen(public_path().'/assets/static/NOVO FRETE CD Correto (1).csv',"r")) !== FALSE){

            while (($data2 = fgetcsv($file2, 0, ';')) !== FALSE){ //le uma linha do csv
                array_push($dataCsv2, $data2);
            }
            fclose($file2);
        }

        //percorre o array dos dados vindos do csv e adiciona nos respectivos vetores
        for($i = 4; $i < count($dataCsv2); $i++){
            array_push($this->SZEUB, ['shipping_price' => (float)str_replace("$","",str_replace(",",".",$dataCsv2[$i][2])), 'handling_fee' => 0 ]);
        }
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
     *      weight,
     *      quantity
     * ]
     * */

    public function calcBoxWeight(array $products){

        try {
            //$total_weight = 0;
            //$max_packing_weight = 0;
            // Calcula o peso total dos produtos + embalagem
            foreach($products as $product){
                $this->weight += ($product['qty'] * $product['weight']) + ($product['qty'] * $product['packing_weight']);
                //Atualmente o peso total é a quantidade de produtos vezes o seu peso
                //Cada produto tem uma embalagem respectiva, então multiplica a quantidade pela embalagem também

                // ALTERAÇÃO RETIRADA POR ENQUANTO
                // if($product['packing_weight'] > $max_packing_weight){ //pega o maior valor de embalagem entre os produtos
                //     $max_packing_weight = $product['packing_weight'];
                // }
            }
            //$this->max_packing_weight = $max_packing_weight;
            //$this->weight = $total_weight;
            //$this->weight = $total_weight < 1 ? 1 + $max_packing_weight : $total_weight + $max_packing_weight;
            //$this->weight = $total_weight < 1 ? 1 + $max_packing_weight : $total_weight + ($max_packing_weight * ($total_weight / 2000));
            //multiplica o valor de embalagem pela quantidade de caixas, cada caixa contem 2000gramas

            //$this->setPrice($total_price);
        }catch(\Exception $e){
            Log::error('Error while setting shipping box weight.', [$e]);

            return false;
        }

        return true;
    }

    public function getShippingPrice(){
        if($this->weight == 0 || !$this->to_zipcode){
            return false;
        }

        try {
            //consulta a cotação atual do dólar
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'http://economia.awesomeapi.com.br/json/USD-BRL');
            if($response->getStatusCode() == 200){
                $dolar = (float)json_decode($response->getBody())[0]->ask; //dólar venda
            }else{
                $dolar = 0;
            }

            //vai abatendo a quantidade máxima de peso e contabilizando quantas "caixas" vão dar, soma tudo e obtem o valor final do frete
            $totalValueShipping = 0.0;

            $totalWeight = $this->weight;

            while($totalWeight > 0){ //enquanto ainda tem valor de peso e possivelmente novas caixas, calcula
                //ALTERAÇÃO RETIRADA POR ENQUANTO -- toda vez q entra aqui, é pq tem uma nova embalagem
                //$totalWeight = $totalWeight + $this->max_packing_weight;

                $index = 0;
                //verifica dentro da estrutura de dados o peso e o tipo de frete
                if($totalWeight <= 2000){ //caso seja um peso dentro do range, calcula o indice
                    if($totalWeight % 10 == 0){ //as bordas superiores
                        $index = (int)floor($totalWeight/10) - 1;
                    }else{ //qualquer outro número dentro do range
                        $index = (int)floor($totalWeight/10);
                    }
                    $totalWeight = 0;
                }else{ //caso seja um peso fora do range pra cima, pega o ultimo valor de frete
                    $totalWeight -= 2000; //subtrai o valor máximo da caixa, o restante volta no loop para ser calculado novamente
                    $index = 199;
                }

                if($this->shippingMethod == 'CDEUB'){
                    $totalValueShipping += ($this->CDEUB[$index]['shipping_price'] + $this->CDEUB[$index]['handling_fee']) * $dolar;
                }

                if($this->shippingMethod == 'PUAM'){
                    $totalValueShipping += ($this->PUAM[$index]['shipping_price'] + $this->PUAM[$index]['handling_fee']) * $dolar;
                }

                if($this->shippingMethod == 'SZEUB'){
                    $totalValueShipping += ($this->SZEUB[$index]['shipping_price'] + $this->SZEUB[$index]['handling_fee']) * $dolar;
                }
            }

            return $totalValueShipping;

        }catch(\Exception $e){
            Log::error('Shipping calc error.', [$e]);

            return false;
        }

        return true;
    }

    protected function parseZipcode($zipcode){
        return preg_replace('/\D/', '', $zipcode);
    }

    public function prepareOrderProducts($order_items){
        $result = [];

        $shippingMethod = 'CDEUB'; //valor padrão do método principal de envio

        foreach($order_items as $order_items){

            //pega o tipo de método de envio do último produto na ordem e atribui esse método para a ordem inteira
            $shippingMethod = $order_items->variant->product->shipping_method_china_division;

            $result[] = [
                'packing_weight' => $order_items->variant->product->packing_weight && is_numeric($order_items->variant->product->packing_weight) ? $order_items->variant->product->packing_weight : 0.0, //peso da embalagem desse produto
                'weight' => $order_items->variant->weight_in_grams,
                'qty' => $order_items->quantity
            ];
        }
        $this->shippingMethod = $shippingMethod;

        return $result;
    }
}
