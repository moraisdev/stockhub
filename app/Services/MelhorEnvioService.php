<?php
namespace App\Services;

use App\Models\SupplierOrders;
use App\Models\Suppliers;
use App\Models\AdminMelhorEnvioSettings ;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Log;

class MelhorEnvioService{
    protected $mawa_tax = 2.0;

    protected $from_zipcode; //cep do fornecedor, mas os dados da etiqueta são os dados do lojista
    protected $to_zipcode; //cep do consumidor final
    protected $settings;

    protected $width = 12;
    protected $height = 4;
    protected $depth = 17;
    protected $weight = 0.4;
    protected $price = 0;
    protected $products = []; //salva os produtos com suas dimensões e deixa a api calcular o tamanho de caixa e quantidade de caixas
    protected $volumes = []; //cria um array com os volumes que resultaram dos produtos passados

    protected $client_id;
    protected $secret;
    protected $token;
    protected $customer; //dados do comprador final para a compra do frete
    protected $shop; //dados do lojista
    protected $supplier; //dados do fornecedor para serem passados na hora de comprar o frete

    //oficial
    protected $linkApi = 'https://melhorenvio.com.br';
    protected $linkCallBack = 'https://localhost:8000/admin/settings';

    //sandbox
    //protected $linkApi = 'https://sandbox.melhorenvio.com.br';
    //protected $linkCallBack = 'https://mawa-melhor-envio.herokuapp.com/admin/settings';
    
    public function __construct(AdminMelhorEnvioSettings $melhorEnvioSettings = NULL){
        if(!$melhorEnvioSettings){ //agora caso seja null, busca o primeiro admin_melhor_envio_settings
            $melhorEnvioSettings1 = AdminMelhorEnvioSettings::find(1);
            $this->settings = $melhorEnvioSettings1;
            $this->client_id = $melhorEnvioSettings1->client_id;
            $this->secret = $melhorEnvioSettings1->secret; //dados do app na conta da Jéssica
            $this->token = $melhorEnvioSettings1->token;
        }else{
            $this->settings = $melhorEnvioSettings;
            $this->client_id = $melhorEnvioSettings->client_id;
            $this->secret = $melhorEnvioSettings->secret; //dados do app na conta da Jéssica
            $this->token = $melhorEnvioSettings->token;
        }

        // $customer = $customer;
        // $shop = $shop;
        // $supplier = $supplier;
        //$this->token = $melhorEnvioSettings->token;
        
        //oficial
        //$this->token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImY5OWU3NjQ0OWMyYjQwNmI4NTU5NmFjNDg3NTExMmUyZDc5N2M4ODcyMDNkZDNhMzk4YzFkMjc4OTc2M2VlYjBiNDllNDEyYjhlYjEyZTZjIn0.eyJhdWQiOiIxIiwianRpIjoiZjk5ZTc2NDQ5YzJiNDA2Yjg1NTk2YWM0ODc1MTEyZTJkNzk3Yzg4NzIwM2RkM2EzOThjMWQyNzg5NzYzZWViMGI0OWU0MTJiOGViMTJlNmMiLCJpYXQiOjE2MTY3ODE1NDYsIm5iZiI6MTYxNjc4MTU0NiwiZXhwIjoxNjQ4MzE3NTQ2LCJzdWIiOiJlN2MzMDJlZC02YWVjLTQ1ZWMtOTNhMy1jNDliNmM4NjdiNGUiLCJzY29wZXMiOlsiY2FydC1yZWFkIiwiY2FydC13cml0ZSIsImNvbXBhbmllcy1yZWFkIiwiY29tcGFuaWVzLXdyaXRlIiwiY291cG9ucy1yZWFkIiwiY291cG9ucy13cml0ZSIsIm5vdGlmaWNhdGlvbnMtcmVhZCIsIm9yZGVycy1yZWFkIiwicHJvZHVjdHMtcmVhZCIsInByb2R1Y3RzLWRlc3Ryb3kiLCJwcm9kdWN0cy13cml0ZSIsInB1cmNoYXNlcy1yZWFkIiwic2hpcHBpbmctY2FsY3VsYXRlIiwic2hpcHBpbmctY2FuY2VsIiwic2hpcHBpbmctY2hlY2tvdXQiLCJzaGlwcGluZy1jb21wYW5pZXMiLCJzaGlwcGluZy1nZW5lcmF0ZSIsInNoaXBwaW5nLXByZXZpZXciLCJzaGlwcGluZy1wcmludCIsInNoaXBwaW5nLXNoYXJlIiwic2hpcHBpbmctdHJhY2tpbmciLCJlY29tbWVyY2Utc2hpcHBpbmciLCJ0cmFuc2FjdGlvbnMtcmVhZCIsInVzZXJzLXJlYWQiLCJ1c2Vycy13cml0ZSIsIndlYmhvb2tzLXJlYWQiLCJ3ZWJob29rcy13cml0ZSJdfQ.O6wBMFigmw8HBZrSasSDpW_Rp6miMRl5W-VBCcVIP-tVugRyEyi_Rcu9N0LXdJpLclajnHsh4fJsNyldJzYz-PA8jUM8K2h-aP81vK4CS792-Tnk3EPoxsasnY8YN0B_VQ3-wYGhWg8ADe5Wa5-jnyKLfsEU33nhqPlT2V3TIgHC2HNlatCGv0CLwDaBeLvbCJdBoDNs18jn37UTku_MNuA6rcHQygW91iIh6yzf5Ct5j0CG820eB-zKxeIqjRXukmz3yL6wmsFbJpmKg5UCmUoZ9ztWXjX0SEskXDYNmBSyTQrpTWu9vW1tbud1pLy9249q8KpT1y8WNQJWEevp2-VsUR4d9S9Hw2bCTWYO6oX8KBv5_F5hSu-6Y_CUTfNUFrlPZUeOdrAVM8TJNh838SnCjdb1yfykY7tLaeZ7T6NgtcRY-Qu94_11Z3925Delyp3GD67i_RfJdqtrUk0ZZtWMFESrEvk3zOCWw7ugdnNEzgBb56dMnrQEH85O9oXO5dqQ3BiHzXGJi9xueHpQ9BqZRRrN8I0jxKKjnv8xEJ9znUmARzPnvOLCPOAVMm5K3UkO45ABOZkePEkvZrzchMoZQ8LRZgAXuXas_tDOCckKxF0Dg6YXqVh2w1CilvJwCYpU_c0BQ1mIjy9G_XKr71BBstBHZpcdTzHZGIfXPMk";

        //sandbox
        //$this->token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImUwYzJiODIyYzBiOWE3YWIwYjNjMDQ1OWM3YjBiNmQyNDUwMzRlZTA2ZWM4OTIzOWQ3MTE4NmQwM2UzMjgyNGU0Y2IwNmY3NDk4MzM4N2FjIn0.eyJhdWQiOiI5NTYiLCJqdGkiOiJlMGMyYjgyMmMwYjlhN2FiMGIzYzA0NTljN2IwYjZkMjQ1MDM0ZWUwNmVjODkyMzlkNzExODZkMDNlMzI4MjRlNGNiMDZmNzQ5ODMzODdhYyIsImlhdCI6MTYxNzE5ODM1MSwibmJmIjoxNjE3MTk4MzUxLCJleHAiOjE2NDg3MzQzNTEsInN1YiI6ImMyZDFiYzhlLTkxNWYtNDcxMy1iYWJhLWVjNjI2NmI4NTk1ZiIsInNjb3BlcyI6WyJjYXJ0LXJlYWQiLCJjYXJ0LXdyaXRlIiwiY29tcGFuaWVzLXJlYWQiLCJjb21wYW5pZXMtd3JpdGUiLCJjb3Vwb25zLXJlYWQiLCJjb3Vwb25zLXdyaXRlIiwibm90aWZpY2F0aW9ucy1yZWFkIiwib3JkZXJzLXJlYWQiLCJwcm9kdWN0cy1yZWFkIiwicHJvZHVjdHMtZGVzdHJveSIsInByb2R1Y3RzLXdyaXRlIiwicHVyY2hhc2VzLXJlYWQiLCJzaGlwcGluZy1jYWxjdWxhdGUiLCJzaGlwcGluZy1jYW5jZWwiLCJzaGlwcGluZy1jaGVja291dCIsInNoaXBwaW5nLWNvbXBhbmllcyIsInNoaXBwaW5nLWdlbmVyYXRlIiwic2hpcHBpbmctcHJldmlldyIsInNoaXBwaW5nLXByaW50Iiwic2hpcHBpbmctc2hhcmUiLCJzaGlwcGluZy10cmFja2luZyIsImVjb21tZXJjZS1zaGlwcGluZyIsInRyYW5zYWN0aW9ucy1yZWFkIiwidXNlcnMtcmVhZCIsInVzZXJzLXdyaXRlIiwid2ViaG9va3MtcmVhZCIsIndlYmhvb2tzLXdyaXRlIl19.HDqjo0LJmxK27mz7Go_JOO_Z8kAPpBRoWOs1SVXSflISVAn1Wh527Soq69fSGVUaTGzAFYoqvoA8KkovwzzxEg4uhRdnMsUm8xg9A8kB21WxUQ5QBdzbB6j2qBOdqFWmCtd6dXwDdEe0ftypflMwndY-A3rsfF8fV_GHDS7muGFp0tKJW8u9rCGEB-uszm9T3CelJSkRL_NhjyZ00PeJqPHoMRMPrtNEaKq_ONeOvA5WXW3bCQvVVyY5M0NlFor8fOhfXTVISFImkdEGshm1S42Ta7ruBYFR484s4zVAsjyUWbvQJTVp2IUnOgBOKs1NEleUcLlG2dqrcRg8dmf2Rvx0jQz3Ta92r6dzjrTdjXIlrDf0bvtPOcRUNENpQZvCJufm3x_A2ZGHZk57yq2eosg5nGqqHoYH1yTAxktvhrz5_0XIxFRyIB1R8EnREnRqQjKpWaSYd0JKW7g-V9uATxikhbX3ESc5QochqnEQqsuJvs2-VAsZtWwWflOWRGrZS0hl-jmf-JBokZz7p-j44c-HAxAmlmEzzSPCDP1hYvgIYnGnbvlrYPfjs275URrYYYngTPnxkKFJK439KQMfiVZhTfQ7dxQJ01ScCmj5ppPpAAC0Ui9j8soHMfaageqTMOs__7HM28Pa34VfTxM6bjCqE0B3GVsMykq-EaZqK7k";
        
        //verifica se está autenticado, caso não esteja, solicita um novo token
        if($this->settings->code){ //caso ja tenha um code, quer dizer que não é a primeira vez que cadastra
            self::verifyAuth();
        }
    }

    public function verifyAuth(){
        //tenta pegar um novo token caso receba Unauthenticated
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $this->linkApi.'/api/v2/me/shipment/app-settings', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ]
            ]);

            $responseSettings = json_decode($response->getBody());

            //dd($response->getStatusCode());
            if($responseSettings && $responseSettings->settings){
                return true;
            }
        } catch (\Exception $e) {
            Log::error('verifyAuth error.', [$e]);
            report($e);
            
            if($e->getResponse()->getStatusCode() == 401){ //401 - Unauthorized
                //solicita um novo token
                self::getToken($this->settings->code);
            }
        }
    }

    public function getLinkTags($tags){
        //dd($tags);
        //gera o link da etiqueta e salva
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/print', [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Mawa Post dev@mawapost.com',
                'Authorization' => 'Bearer '.$this->token
            ],
            'json' => [
                'mode' => 'public', //qualquer um com o link pode acessar e imprimir
                "orders" => $tags
            ]
        ]);

        $responseLinkEtiquetas = json_decode($response->getBody());
        if($responseLinkEtiquetas && $responseLinkEtiquetas->url != ''){
            return $responseLinkEtiquetas->url;
        }

        return false;
    }

    public function verifyIsSent($freightId){
        //verifica se um frete já foi enviado
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->linkApi.'/api/v2/me/orders/search?q='.$freightId, [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Mawa Post dev@mawapost.com',
                'Authorization' => 'Bearer '.$this->token
            ]
        ]);
        
        $responseIsSent = json_decode($response->getBody());
        if($responseIsSent && $responseIsSent[0]->status == 'posted'){
            return true;
        }

        return false;
    }

    public function updateStatusFreight($freightId){
        try {
            //verifica se um frete já foi enviado
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $this->linkApi.'/api/v2/me/orders/search?q='.$freightId, [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ]
            ]);
            
            $responseStatus = json_decode($response->getBody());
            if($responseStatus && !isset($responseStatus->message)){
                return $responseStatus[0];
            }

            return NULL;
        } catch(\Exception $e){
            //Log::error('updateStatusFreight error.', [$e]);
            return NULL;
        }
    }

    public function getToken($code){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', $this->linkApi.'/oauth/token', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com'
                ],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->client_id,
                    'client_secret' => $this->secret,
                    'redirect_uri' => $this->linkCallBack,
                    'code' => $code
                ]
            ]);
            $responseToken = json_decode($response->getBody());
            if($responseToken && $this->settings){
                $this->settings->token = $responseToken->access_token;
                $this->settings->refresh_token = $responseToken->refresh_token;
                $this->settings->code = $code; //salva o code tbm
                if($this->settings->save()){
                    return true;
                }
            }
            return false;

        } catch(\Exception $e){
            $status_code = $e->getCode();
            report($e);

            //caso tenha sido uma bad request, pode ser por conta de ja existir um token, então faz novamente a requisição pra ver se vem o refresh token
            if($e->getResponse()->getStatusCode() == 400 && $this->settings->refresh_token){
                self::getTokenRefresh($this->settings->refresh_token);
            }
        }
    }

    public function getTokenRefresh($refresh_token){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', $this->linkApi.'/oauth/token', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com'
                ],
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => $this->client_id,
                    'client_secret' => $this->secret,
                    'refresh_token' => $refresh_token
                ]
            ]);
            $responseToken = json_decode($response->getBody());
            if($responseToken && $this->settings){
                $this->settings->token = $responseToken->access_token;
                $this->settings->refresh_token = $responseToken->refresh_token;
                if($this->settings->save()){
                    return true;
                }
            }
            return false;
        } catch(\Exception $e){
            $status_code = $e->getCode();
            //report($e);
        }
    }

    public function getAuth(){
        return redirect($this->linkApi.'/oauth/authorize?client_id='.$this->client_id.'&redirect_uri='.$this->linkCallBack.'&response_type=code&scope=cart-read cart-write companies-read companies-write coupons-read coupons-write notifications-read orders-read products-read products-write purchases-read shipping-calculate shipping-cancel shipping-checkout shipping-companies shipping-generate shipping-preview shipping-print shipping-share shipping-tracking ecommerce-shipping transactions-read users-read users-write');
    }

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

    protected function parseZipcode($zipcode){
        return preg_replace('/\D/', '', $zipcode);
    }

    //realiza somente a quotação
    public function quoteFreight(){
        $client = new \GuzzleHttp\Client();

        //faz a requisição que realiza a cotação
        $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/calculate', [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Mawa Post dev@mawapost.com',
                'Authorization' => 'Bearer '.$this->token
            ],
            'json' => [
                'from' => [
                    'postal_code' => $this->from_zipcode //cep do fornecedor
                ],
                'to' => [
                    'postal_code' => $this->to_zipcode //cep do consumidor final
                ],
                'products' => $this->products
            ]
        ]);

        //faz um novo objeto somando a taxa
        $responseBody = array();
        
        $quotes = json_decode($response->getBody());
        
        foreach ($quotes as $quote) {
            if(isset($quote->price)){ //caso tenha retornado o preço
                array_push($responseBody, [
                    'name' => $quote->name,
                    'price' => $quote->price + $this->mawa_tax,
                    'company' => [
                        'picture' => $quote->company->picture
                    ]
                ]);
            }else{ //erro
                array_push($responseBody, [
                    'name' => $quote->name,
                    'error' => $quote->error,
                    'company' => [
                        'picture' => $quote->company->picture
                    ]
                ]);
            }
        }
        
        return $responseBody;
    }

    //retorna o menor valor da cotação
    public function quoteFreightMinValue(){
        $client = new \GuzzleHttp\Client();

        //faz a requisição que realiza a cotação
        $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/calculate', [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Mawa Post dev@mawapost.com',
                'Authorization' => 'Bearer '.$this->token
            ],
            'json' => [
                'from' => [
                    'postal_code' => $this->from_zipcode //cep do fornecedor
                ],
                'to' => [
                    'postal_code' => $this->to_zipcode //cep do consumidor final
                ],
                'products' => $this->products
            ]
        ]);

        //faz um novo objeto somando a taxa
        $responseBody = array();
        
        $quotes = json_decode($response->getBody());
        foreach ($quotes as $quote) {
            if(isset($quote->price)){ //caso tenha retornado o preço
                array_push($responseBody, (object)[
                    'name' => $quote->name,
                    'price' => $quote->price + $this->mawa_tax,
                    'company' => [
                        'picture' => $quote->company->picture
                    ]
                ]);
            }else{ //erro
                array_push($responseBody, (object)[
                    'name' => $quote->name,
                    'error' => $quote->error,
                    'company' => [
                        'picture' => $quote->company->picture
                    ]
                ]);
            }
        }

        $minQuote = 9999.99;
        foreach ($responseBody as $quote) {
            if(isset($quote->price) && $quote->price > 0 && $quote->price < $minQuote ){
                $minQuote = $quote->price;
            }
        }
        
        return $minQuote;
    }

    //realiza a cotação novamente e compra o frete
    public function quoteBuyFreight($supplier, $shop, $customer, $order , $customerd){
       // if($this->width == 0 || $this->height == 0 || $this->depth == 0 || $this->weight == 0 || !$this->to_zipcode){
       //     return false;
       // }
   
       try {    
        $orderitems =  OrderItems::where('order_id', $order->id)->get();
        $products = [];
        foreach ($orderitems as $order_item) {
              
              $products[] = [
                'width' => $order_item->variant->width && $order_item->variant->width > 0 ? $order_item->variant->width : 1,
                'height' => $order_item->variant->height && $order_item->variant->height > 0 ? $order_item->variant->height : 1,
                'length' => $order_item->variant->depth && $order_item->variant->depth > 0 ? $order_item->variant->depth : 1,
                'weight' => $order_item->variant->weight_in_grams && $order_item->variant->weight_in_grams > 0 ? $order_item->variant->weight_in_grams / 1000 : 1, //pois o peso está em gramas e o cálculo é feito em kilos
                'quantity' => $order_item->quantity && $order_item->quantity > 0 ? $order_item->quantity : 1,
                'unitary_value' => $order_item->external_price && floatval($order_item->external_price) > 0 ? floatval($order_item->external_price) : 0,
                'name' => $order_item->title,
                'id' => $order_item->sku
            ];
        }    

     
        $client = new \GuzzleHttp\Client();
        //faz a requisição que realiza a cotação
        $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/calculate', [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Mawa Post dev@mawapost.com',
                'Authorization' => 'Bearer '.$this->token
            ],
            'json' => [
                'from' => [
                    'postal_code' => $supplier->address->zipcode //cep do fornecedor
                ],
                'to' => [
                    'postal_code' => $customer->zipcode //cep do consumidor final
                ],
            
                'products' => $products
            ]
        ]);
        
        //percorre o array de serviços buscando o menor valor de cotação para os dados passados
        $quotes = json_decode($response->getBody());
        
       

        $minQuote = (object)[
            'value' => 9999.99,
            'service' => 0,
            'name' => ''
        ];

        foreach ($quotes as $quote) {
            if(isset($quote->price) && $quote->price > 0 && $quote->price < $minQuote->value ){
                $minQuote->value = $quote->price;
                $minQuote->service = $quote->id; //id do serviço 1 - PAC, 2 - SEDEX, 3 - MINI ENVIOS
                $minQuote->name = $quote->name;

                //alem disso, pega os dados do pacote para adicionar no carrinho posteriormente
                $this->volumes = []; //zera o array de volumes, caso tenha algo
                foreach ($quote->packages as $package) {
                    $this->volumes[] = [
                        'height' => $package->dimensions->height,
                        'width' => $package->dimensions->width,
                        'length' => $package->dimensions->length,
                        'weight' => floatval($package->weight)
                    ];
                }
            }
        }

        //como esse é o método escolhido, ja faz a compra do frete e paga ele na melhor envio
        //adiciona frete no carrinho
        
   $validdocumet = strlen($shop->document);

   if ($validdocumet == 11){
    $documentcpf = $shop->document;
    $documentcnpj = 0;
   }else{
    $documentcnpj = $shop->document;
    $documentcpf = 0;
   }
        if ($documentcpf != 0) {
            $response = $client->request('POST', $this->linkApi.'/api/v2/me/cart', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
                
                'json' => [
                    "service" => $minQuote->service,
                    'insurance_value' => '1.00',
                    "from" => [
                        "name" => $shop->name,
                        "phone" => $shop->phone ? $shop->phone : '31996980914',
                        "email" => $shop->email,
                        "document" => $shop->document,
                        "address" => $supplier->address->street,
                        "complement" => $supplier->address->complement ? $supplier->address->complement : '',
                        "number" => $supplier->address->number ? $supplier->address->number : '',
                        "district" => $supplier->address->district ? $supplier->address->district : '',
                        "city" => $supplier->address->city ? $supplier->address->city : '',
                        "country_id" => $supplier->address->country && strtoupper($supplier->address->country) == 'BRASIL' || strtoupper($supplier->address->country) == 'BRAZIL' ? 'BR' : '',
                        "postal_code" => $supplier->address->zipcode
                    ],
                    "to" => [
                        "name" => $customer->name,
                        "phone" => $customer->phone ? $customer->phone : '31996980914',
                        "email" => $customer->email,
                        "address" => $customer->address1,
                        "district" => $customer->address2 ? $customer->address2 : '',
                        "city" => $customer->city ? $customer->city : '',
                        "document" => $customerd->cpf,
                        //"country_id" => $customer->address->country && strtoupper($customer->address->country) == 'BRASIL' || strtoupper($customer->address->country) == 'BRAZIL'? 'BR' : '',
                        "country_id" => 'BR',
                        "postal_code" => $customer->zipcode

                    ],
                    
                    'products' => $products,
                    'volumes' => $this->volumes,
                    "options" => [
                        "insurance_value" => '1.00',
                        "receipt" => false,
                        "own_hand" => false,
                        "reverse" => false,
                        "non_commercial" => true,
                        "platform" => "Mawa Post",
                        "tags" => [
                            [
                                "tag" => "ShopMix ID Lojista ".$order->name,
                                "url" => NULL
                            ]
                        ]
                    ]
                ]
            ]);

        } elseif ($documentcnpj != 0){
            $response = $client->request('POST', $this->linkApi.'/api/v2/me/cart', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
                
                'json' => [
                    "service" => $minQuote->service,
                    "from" => [
                        "name" => $shop->name,
                        "phone" => $shop->phone ? $shop->phone : '31996980914',
                        "email" => $shop->email,
                        "company_document" => $shop->document,
                        "address" => $supplier->address->street,
                        "complement" => $supplier->address->complement ? $supplier->address->complement : '',
                        "number" => $supplier->address->number ? $supplier->address->number : '',
                        "district" => $supplier->address->district ? $supplier->address->district : '',
                        "city" => $supplier->address->city ? $supplier->address->city : '',
                        "country_id" => $supplier->address->country && strtoupper($supplier->address->country) == 'BRASIL' || strtoupper($supplier->address->country) == 'BRAZIL' ? 'BR' : '',
                        "postal_code" => $supplier->address->zipcode
                    ],
                    "to" => [
                        "name" => $customer->name,
                        "phone" => $customer->phone ? $customer->phone : '31996980914',
                        "email" => $customer->email,
                        "address" => $customer->address1,
                        "district" => $customer->address2 ? $customer->address2 : '',
                        "city" => $customer->city ? $customer->city : '',
                        "document" => $customerd->cpf,
                        //"country_id" => $customer->address->country && strtoupper($customer->address->country) == 'BRASIL' || strtoupper($customer->address->country) == 'BRAZIL'? 'BR' : '',
                        "country_id" => 'BR',
                        "postal_code" => $customer->zipcode
                    ],
                    
                    'products' => $this->products,
                    'volumes' => $this->volumes,
                    "options" => [
                        "insurance_value" => '1.00',
                        "receipt" => false,
                        "own_hand" => false,
                        "reverse" => false,
                        "non_commercial" => true,
                        "platform" => "Mawa Post",
                        "tags" => [
                            [
                                "tag" => "ShopMix ID Lojista ".$order->name,
                                "url" => NULL
                            ]
                        ]
                    ]
                ]
            ]);


        }
                    
        
         $responseFreight = json_decode($response->getBody());

        if($responseFreight->id && $responseFreight->price && $responseFreight->price > 0){ //caso tenha gerado o id corretamente o valor também
            return (object)[
                'valor' => $responseFreight->price,
                'freteId' => $responseFreight->id,
                'serviceId' => $minQuote->service,
                'status' => $responseFreight->status,
                'protocol' => $responseFreight->protocol,
            ];
        }

        return $responseFreight;
    }catch(\Exception $e){
        Log::error('quoteBuyFreight error', [$e]);
        return false;
    }
}


    public function generateTag($freightId){
        try {
            $client = new \GuzzleHttp\Client();

            $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/generate', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
                'json' => [
                    'mode' => 'public', //qualquer um com o link pode acessar e imprimir
                    "orders" => [
                        $freightId,
                    ]
                ]
            ]);

            if(isset(json_decode($response->getBody())->$freightId) && json_decode($response->getBody())->$freightId->status){ //etiqueta gerada com sucesso
                return true;
            }
            return false;
        } catch(\Exception $e){
            //Log::error('generateTag error.', [$e]);
            return false;
        }
    }

    public function printTag($freightId){
        try {
            $client = new \GuzzleHttp\Client();
            
            //gera o link da etiqueta
            $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/print', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
                'json' => [
                    'mode' => 'public', //qualquer um com o link pode acessar e imprimir
                    "orders" => [
                        $freightId,
                    ]
                ]
            ]);
            
            $responseBody = json_decode($response->getBody());
            if($responseBody->url){
                return $responseBody;
            }

            return false;
        } catch(\Exception $e){
            //Log::error('printTag error.', [$e]);
            return false;
        }
    }

    public function payCartFreight($freteMelhorEnvio){
        $freightId = $freteMelhorEnvio->melhor_envio_id;
        //paga o carrinho gerado e retorna o número de rastreio
        //também já gera as etiquetas
        //pega o id do item no carrinho e realiza o pagamento com o saldo que existe na conta do fornecedor da melhor envio
        //posteriormente vai ser implementado o checkout com outros gateways
        try {
            //code...
            $client = new \GuzzleHttp\Client();

            //verifica primeiro se o frete já não foi pago
            $response = $client->request('GET', $this->linkApi.'/api/v2/me/orders/search?q='.$freteMelhorEnvio->protocol, [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
            ]);
            
            $responseTracking = json_decode($response->getBody());
            //dd($responseTracking);
            //caso já tenha sido pago
            if($responseTracking && ($responseTracking[0]->status == 'paid' || $responseTracking[0]->status == 'released' || $responseTracking[0]->status == 'posted')){
                //verifica se ainda não foi gerada a etiqueta, caso não tenha sido gerada, gera e gera o link
                if(!$responseTracking[0]->tracking){
                    //caso não tenha gerado, gera a etiqueta
                    //para gerar o código de rastreio é necessário gerar a etiqueta (que tem validade de 7 dias)
                    $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/generate', [
                        'headers' => [
                            'Accept' => 'application/json',
                            'User-Agent' => 'Mawa Post dev@mawapost.com',
                            'Authorization' => 'Bearer '.$this->token
                        ],
                        'json' => [
                            "orders" => [
                                $freightId,
                            ]
                        ]
                    ]);
                }

                //gera o link da etiqueta e salva
                $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/print', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'User-Agent' => 'Mawa Post dev@mawapost.com',
                        'Authorization' => 'Bearer '.$this->token
                    ],
                    'json' => [
                        'mode' => 'public', //qualquer um com o link pode acessar e imprimir
                        "orders" => [
                            $freightId,
                        ]
                    ]
                ]);

                //caso o status da requisição seja 200, a etiqueta foi gerada com sucesso
                if(json_decode($response->getBody())->$freightId->status){                    

                    $responseEtiqueta = json_decode($response->getBody());
                    $linkEtiqueta = '';
                    if($responseEtiqueta && $responseEtiqueta->url && $responseEtiqueta->url != ''){
                        $linkEtiqueta = $responseEtiqueta->url;
                    }

                    //pega o código de rastreio
                    $response = $client->request('GET', $this->linkApi.'/api/v2/me/orders/search?q='.$freightId, [
                        'headers' => [
                            'Accept' => 'application/json',
                            'User-Agent' => 'Mawa Post dev@mawapost.com',
                            'Authorization' => 'Bearer '.$this->token
                        ],
                    ]);
                    
                    $responseTracking = json_decode($response->getBody());

                    if($responseTracking){
                        return (object)[
                            'status' => $responseTracking[0]->status ? $responseTracking[0]->status : '',
                            'tracking' => $responseTracking[0]->tracking ? $responseTracking[0]->tracking : '',
                            'company' => $responseTracking[0]->service->company->name ? $responseTracking[0]->service->company->name.' '.$responseTracking[0]->service->name : '',
                            'link' => $responseTracking[0]->tracking ? 'https://www.melhorrastreio.com.br/rastreio/'.$responseTracking[0]->tracking : '',
                            'tag_url' => $linkEtiqueta
                        ];
                    }
                    return false;                
                }
            }

            $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/checkout', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
                'json' => [
                    "orders" => [
                        $freightId,
                    ]
                ]
            ]);

            //para gerar o código de rastreio é necessário gerar a etiqueta (que tem validade de 7 dias)
            $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/generate', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
                'json' => [
                    "orders" => [
                        $freightId,
                    ]
                ]
            ]);           
            
            //caso o status da requisição seja 200, a etiqueta foi gerada com sucesso
            if(json_decode($response->getBody())->$freightId->status){
                //gera o link da etiqueta e salva
                $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/print', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'User-Agent' => 'Mawa Post dev@mawapost.com',
                        'Authorization' => 'Bearer '.$this->token
                    ],
                    'json' => [
                        'mode' => 'public', //qualquer um com o link pode acessar e imprimir
                        "orders" => [
                            $freightId,
                        ]
                    ]
                ]);

                $responseEtiqueta = json_decode($response->getBody());

                $linkEtiqueta = '';
                if($responseEtiqueta && $responseEtiqueta->url && $responseEtiqueta->url != ''){
                    $linkEtiqueta = $responseEtiqueta->url;
                }

                //pega o código de rastreio
                $response = $client->request('GET', $this->linkApi.'/api/v2/me/orders/search?q='.$freightId, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'User-Agent' => 'Mawa Post dev@mawapost.com',
                        'Authorization' => 'Bearer '.$this->token
                    ],
                ]);
                
                $responseTracking = json_decode($response->getBody());

                if($responseTracking){
                    return (object)[
                        'status' => $responseTracking[0]->status ? $responseTracking[0]->status : '',
                        'tracking' => $responseTracking[0]->tracking ? $responseTracking[0]->tracking : '',
                        'company' => $responseTracking[0]->service->company->name ? $responseTracking[0]->service->company->name.' '.$responseTracking[0]->service->name : '',
                        'link' => $responseTracking[0]->tracking ? 'https://www.melhorrastreio.com.br/rastreio/'.$responseTracking[0]->tracking : '',
                        'tag_url' => $linkEtiqueta
                    ];
                }
                return false;                
            }
        }catch(\Exception $e){
            Log::error('payCartFreight error.', [$e]);

            return false;
        }
    }

    public function prepareOrderProducts($order_items){
        //$this->$products = [];
        foreach($order_items as $order_item){
            //só adiciona se o fornecedor desse produto utilizar melhor envio
            if($order_item->variant->product->supplier->shipping_method == 'melhor_envio'){
                $this->products[] = [
                    'width' => $order_item->variant->width && $order_item->variant->width > 0 ? $order_item->variant->width : 1,
                    'height' => $order_item->variant->height && $order_item->variant->height > 0 ? $order_item->variant->height : 1,
                    'length' => $order_item->variant->depth && $order_item->variant->depth > 0 ? $order_item->variant->depth : 1,
                    'weight' => $order_item->variant->weight_in_grams && $order_item->variant->weight_in_grams > 0 ? $order_item->variant->weight_in_grams / 1000 : 1, //pois o peso está em gramas e o cálculo é feito em kilos
                    'quantity' => $order_item->quantity && $order_item->quantity > 0 ? $order_item->quantity : 1,
                    'unitary_value' => $order_item->external_price && floatval($order_item->external_price) > 0 ? floatval($order_item->external_price) : 0,
                    'name' => $order_item->variant->title,
                    'id' => $order_item->variant->id
                ];
            }
        }
        return $this->products;
    }

    public function prepareOrderProductsSupplier($order_items){
        //$this->$products = [];
        foreach($order_items as $order_item){
            if($order_item->variant->product->supplier->shipping_method == 'melhor_envio'){
                $this->products[] = [
                    'width' => $order_item->variant->width && $order_item->variant->width > 0 ? $order_item->variant->width : 1,
                    'height' => $order_item->variant->height && $order_item->variant->height > 0 ? $order_item->variant->height : 1,
                    'length' => $order_item->variant->depth && $order_item->variant->depth > 0 ? $order_item->variant->depth : 1,
                    'weight' => $order_item->variant->weight_in_grams && $order_item->variant->weight_in_grams > 0 ? $order_item->variant->weight_in_grams / 1000 : 1, //pois o peso está em gramas e o cálculo é feito em kilos
                    'quantity' => $order_item->quantity && $order_item->quantity > 0 ? $order_item->quantity : 1,
                    'unitary_value' => $order_item->amount && floatval($order_item->amount) > 0 ? floatval($order_item->amount) : 0,
                    'name' => $order_item->variant->title,
                    'id' => $order_item->variant->id
                ];
            }
        }
        return $this->products;
    }

    public function prepareSimulateProducts($variants){
        //$this->$products = [];
        foreach($variants as $variant){
            if($variant->product->supplier->shipping_method == 'melhor_envio'){
                $this->products[] = [
                    'width' => $variant->width && $variant->width > 0 ? $variant->width : 1,
                    'height' => $variant->height && $variant->height > 0 ? $variant->height : 1,
                    'length' => $variant->depth && $variant->depth > 0 ? $variant->depth : 1,
                    'weight' => $variant->weight_in_grams && $variant->weight_in_grams > 0 ? $variant->weight_in_grams / 1000 : 1, //pois o peso está em gramas e o cálculo é feito em kilos
                    'quantity' => $variant->quantity && $variant->quantity > 0 ? $variant->quantity : 1,
                    'unitary_value' => $variant->external_price && floatval($variant->external_price) > 0 ? floatval($variant->external_price) : 0,
                    'name' => $variant->title,
                    'id' => $variant->id
                ];
            }
        }
        return $this->products;
    }

    public function consultProtocol($protocol){
        try {
            $client = new \GuzzleHttp\Client();

            $response = $client->request('GET', $this->linkApi.'/api/v2/me/orders/search?q='.$protocol, [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
            ]);
            
            $responseTracking = json_decode($response->getBody());
            
            if($responseTracking[0]){
                return $responseTracking[0];
            }
            
            return false;
        }catch(\Exception $e){
            Log::error('consult protocol error.', [$e]);

            return false;
        }
    }

    public function consultProt($protocol){
        try {
            $client = new \GuzzleHttp\Client();

            $response = $client->request('GET', $this->linkApi.'/api/v2/me/orders/search?q='.$protocol, [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
            ]);
            
            $responseTracking = json_decode($response->getBody());
            
           
            
            return $responseTracking;
        }catch(\Exception $e){
            Log::error('consult protocol error.', [$e]);

            return false;
        }
    }



    public function consultRastreio($freightId){
        try {
            $client = new \GuzzleHttp\Client();

            $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/generate', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
                'json' => [
                    'mode' => 'public', //qualquer um com o link pode acessar e imprimir
                    "orders" => [
                        $freightId,
                    ]
                ]
            ]);

            if(isset(json_decode($response->getBody())->$freightId) && json_decode($response->getBody())->$freightId->status){ //etiqueta gerada com sucesso
                return true;
            }
            return false;
        } catch(\Exception $e){
            //Log::error('generateTag error.', [$e]);
            return false;
        }
    }


    public function payCartNew($freightId){
       
        //paga o carrinho gerado e retorna o número de rastreio
        //também já gera as etiquetas
        //pega o id do item no carrinho e realiza o pagamento com o saldo que existe na conta do fornecedor da melhor envio
        //posteriormente vai ser implementado o checkout com outros gateways
        try {
            //code...
            $client = new \GuzzleHttp\Client();

            
            
            $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/checkout', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
                'json' => [
                    "orders" => [
                        $freightId,
                    ]
                ]
            ]);

            //para gerar o código de rastreio é necessário gerar a etiqueta (que tem validade de 7 dias)
            $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/generate', [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mawa Post dev@mawapost.com',
                    'Authorization' => 'Bearer '.$this->token
                ],
                'json' => [
                    "orders" => [
                        $freightId,
                    ]
                ]
            ]);           
            
            //caso o status da requisição seja 200, a etiqueta foi gerada com sucesso
            if(json_decode($response->getBody())->$freightId->status){
                //gera o link da etiqueta e salva
                $response = $client->request('POST', $this->linkApi.'/api/v2/me/shipment/print', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'User-Agent' => 'Mawa Post dev@mawapost.com',
                        'Authorization' => 'Bearer '.$this->token
                    ],
                    'json' => [
                        'mode' => 'public', //qualquer um com o link pode acessar e imprimir
                        "orders" => [
                            $freightId,
                        ]
                    ]
                ]);

                $responseEtiqueta = json_decode($response->getBody());

                $linkEtiqueta = '';
                if($responseEtiqueta && $responseEtiqueta->url && $responseEtiqueta->url != ''){
                    $linkEtiqueta = $responseEtiqueta->url;
                }

                //pega o código de rastreio
                $response = $client->request('GET', $this->linkApi.'/api/v2/me/orders/search?q='.$freightId, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'User-Agent' => 'Mawa Post dev@mawapost.com',
                        'Authorization' => 'Bearer '.$this->token
                    ],
                ]);
                
                $responseTracking = json_decode($response->getBody());                
                return $responseTracking;                
            }
        }catch(\Exception $e){
            Log::error('payCartFreight error.', [$e]);

            return false;
        }
    }


}