<?php
namespace App\Services;

class CurrencyService{

    public static function getDollarPrice(){
        $return = json_decode(file_get_contents('http://economia.awesomeapi.com.br/json/usd'));
        if(isset($return[0])){
            if(isset($return[0]->code) && $return[0]->code == 'USD'){
                $dolarPrice = $return[0]->ask;

                return ['status' => 'success', 'price' => $dolarPrice];
            }else{
                return ['status' => 'error', 'message' => 'Não foi possível consultar o valor do dólar.'];
            }
        }else{
            return ['status' => 'error', 'message' => 'Não foi possível consultar o valor do dólar.'];
        }
    }

}
?>
