<?php

namespace App\Services;
use App\Models\Suppliers;
use App\Models\ProductVariants;
use App\Models\Products;
use Safe2Pay\API\PaymentRequest;
use Safe2Pay\API\RefundType;
use Safe2Pay\Models\Payment\BankSlip;
use Safe2Pay\Models\Payment\Cryptocoin;
use Safe2Pay\Models\Payment\CreditCard;
use Safe2Pay\Models\Payment\DebitCard;
use Safe2Pay\Models\Payment\Carnet;
use Safe2Pay\Models\Payment\CarnetLot;
use Safe2Pay\Models\Transactions\Splits;
use Safe2Pay\Models\Transactions\Transaction;
use Safe2Pay\Models\General\Customer;
use Safe2Pay\Models\General\Product;
use Safe2Pay\Models\General\Address;
use App\Models\ErrorLogs;

use Safe2Pay\Models\Core\Config as Enviroment;
use Safe2Pay\Models\Payment\CarnetBankslip;

$enviroment = new Enviroment();
$enviroment->setAPIKEY('0E7BC01AFBE54003AF330EA32D2ED33C');
//$enviroment->setAPIKEY('2961AABFEC7048D1BF34AA22FD5EEDF5'); //sandbox

class SafeToPayPlansService{
    //private $apikey_sandbox = '2961AABFEC7048D1BF34AA22FD5EEDF5';
    private $apikey = '0E7BC01AFBE54003AF330EA32D2ED33C'; //oficial
    //private $apikey = '2961AABFEC7048D1BF34AA22FD5EEDF5'; //sandbox
    private $sandbox = false; //oficial
    //private $sandbox = true; //sandbox
    private $enderecoCobrancaMawa = [
        "Street" => "Rua José Versolato",
        "Number" => "111",
        "District" => "Centro",
        "ZipCode" => "09750-730",
        "CityName" => "São Bernardo do Campo",
        "StateInitials" => "SP",
        "CountryName" => "Brasil"
    ];

    public function getShopPlans(){
        $opts = array(
            'http'=>array(
              'method'=>"GET",
              'header'=>"X-API-KEY: ".$this->apikey
            )
          );
          
          $context = stream_context_create($opts);
          
          $result = file_get_contents('https://api.safe2pay.com.br/v2/Plan/List?Object.IsEnabled=true&PageNumber=1&RowsPerPage=100', false, $context);
          
          if ($result === FALSE) { /* Handle error */ }
        
          $planos = json_decode($result);

          return $planos->ResponseDetail->Objects;
    }

    public function getShopPlan($planId){
        try {
            //pega os dados de um plano
            $opts = array(
                'http'=>array(
                'method'=>"GET",
                'header'=>"X-API-KEY: ".$this->apikey
                )
            );
            
            $context = stream_context_create($opts);
            
            $result = file_get_contents('https://api.safe2pay.com.br/v2/Plan/Get?id='.$planId, false, $context);
            
            if ($result === FALSE) { /* Handle error */ }
            
            $plano = json_decode($result);

            return $plano->ResponseDetail;
        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return NULL;
        }
        
    }

    public function storeShopSubscriptionTokenCard($shop, $tokenCard, $planId){
        try {
            $payload = array(
                'Plan' => $planId,
                'PaymentMethod' => '2', //cartão de crédito
                "IsSandbox" => $this->sandbox, //retirar isso aqui depois quando for pra produção
                "Customer" => [
                    "Name" => $shop->name,
                    "Identity" => $shop->document,
                    "Phone" => $shop->phone,
                    "Email" => $shop->email,
                    // "Address" => [
                    //     "Street" => $shop->address ? $shop->address->street : '',
                    //     "Number" => $shop->address ? $shop->address->number : '',
                    //     "District" => $shop->address ? $shop->address->district : '',
                    //     "ZipCode" => $shop->address ? $shop->address->zipcode : '',
                    //     "CityName" => $shop->address ? $shop->address->city : '',
                    //     "StateInitials" => $shop->address ? $shop->address->state_code : '',
                    //     "CountryName" => $shop->address ? $shop->address->country : ''
                    // ],
                    "Address" => $this->enderecoCobrancaMawa
                ],
                "IsSendEmail" => true,
                "Emails" => [
                    $shop->email
                ],
                "Token" => $tokenCard
            );
    
            $opts = array(
            "http"=>array(
                "method"=>"POST",
                "header"=>"X-API-KEY: ".$this->apikey."\r\nContent-type: application/json\r\n",
                "content"=> json_encode($payload)
                )
            );
            
            $context = stream_context_create($opts);
    
            $result = file_get_contents('https://api.safe2pay.com.br/v2/Subscription/Add', false, $context);
    
            if ($result === FALSE) { 
                return NULL;    
            }
    
            $result = json_decode($result);

            if($result->HasError){ //caso tenha algum erro
                return [
                    'error' => "Código do Erro: ".$result->ErrorCode." - ".$result->Error,
                    'hasError' => $result->HasError
                ];
            }

            if($result->ResponseDetail->Message == "Pagamento Recusado"){ //caso especial, tem q ver os outros
                return [
                    'error' => "Pagamento Recusado - ".$result->ResponseDetail->Description,
                    'hasError' => TRUE
                ];
            }
    
            return [
                'subscription' => $result->ResponseDetail->Subscription,
                'transaction' => $result->ResponseDetail->Transaction,
                'status' => $result->ResponseDetail->SubscriptionStatus == 1 ? 'Ativa' : $result->ResponseDetail->SubscriptionStatus,
                'hasError' => $result->HasError
            ];
        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return NULL;
        }  
    }

    public function storeShopInternalSubscription($request, $tokenCard, $product){
        try {
            //$paymentMethod = $request->payment_method == 'credit_card' ? '2' : '2'; //adicionar outros métodos de pagamento no futuro
            $paymentMethod = '2';
            
            $products = array();
            array_push($products, (object)[
                "Code" => $product->code,
                "Description" => $product->name,
                "UnitPrice" => $product->value,
                "Quantity" => $product->quantity
            ]);

            $payload = array(
                'PaymentMethod' => $paymentMethod, //cartão de crédito
                "IsSandbox" => $this->sandbox, //retirar isso aqui depois quando for pra produção,
                "Vendor" => "Planos Mawa",
                "Customer" => [
                    "Name" => $request->name,
                    "Identity" => $request->document,
                    "Phone" => $request->phone,
                    "Email" => $request->email,
                    // "Address" => [
                    //     "Street" => $request->street,
                    //     "Number" => $request->number,
                    //     "District" => $request->district,
                    //     "ZipCode" => $request->zipcode,
                    //     "CityName" => $request->city,
                    //     "StateInitials" => $request->state_code,
                    //     "CountryName" => $request->country
                    // ]
                    "Address" => $this->enderecoCobrancaMawa
                ],
                "Products" => $products,
                "PaymentObject" => [
                    "InstallmentQuantity" => $request->installments,
                    "Token" => $tokenCard,
                    "IsApplyInterest" => true
                ],
            );
            //dd($payload);
            $opts = array(
            "http"=>array(
                "method"=>"POST",
                "header"=>"X-API-KEY: ".$this->apikey."\r\nContent-type: application/json\r\n",
                "content"=> json_encode($payload)
                )
            );

            $context = stream_context_create($opts);

            $result = file_get_contents('https://payment.safe2pay.com.br/v2/Payment', false, $context);

            if ($result === FALSE) { /* Handle error */ }
            $resultJson = $result;
            $result = json_decode($result);

            return [
                'transaction' => $result->ResponseDetail->IdTransaction,
                'status' => $result->ResponseDetail->Status == 3 ? 'Ativa' : $result->ResponseDetail->Message,
                'resultJson' => $resultJson
            ];

        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return NULL;
        } 
    }

    public function storeShopInternalSubscriptionV2($shop, $product){
        try {
            //$paymentMethod = $request->payment_method == 'credit_card' ? '2' : '2'; //adicionar outros métodos de pagamento no futuro
            $paymentMethod = '2';
            
            $products = array();
            array_push($products, (object)[
                "Code" => $product->code,
                "Description" => $product->name,
                "UnitPrice" => $product->value,
                "Quantity" => $product->quantity
            ]);

            $payload = array(
                'PaymentMethod' => $paymentMethod, //cartão de crédito
                "IsSandbox" => $this->sandbox, //retirar isso aqui depois quando for pra produção,
                "Vendor" => "Planos Mawa",
                "Customer" => [
                    "Name" => $shop->name,
                    "Identity" => $shop->document,
                    "Phone" => $shop->phone,
                    "Email" => $shop->email,
                    // "Address" => [
                    //     "Street" => $shop->address->street,
                    //     "Number" => $shop->address->number,
                    //     "District" => $shop->address->district,
                    //     "ZipCode" => $shop->address->zipcode,
                    //     "CityName" => $shop->address->city,
                    //     "StateInitials" => $shop->address->state_code,
                    //     "CountryName" => $shop->address->country
                    // ]
                    "Address" => $this->enderecoCobrancaMawa
                ],
                "Products" => $products,
                "PaymentObject" => [
                    "InstallmentQuantity" => $shop->token_card->installments,
                    "Token" => $shop->token_card->token,
                    "IsApplyInterest" => true
                ],
            );

            $opts = array(
            "http"=>array(
                "method"=>"POST",
                "header"=>"X-API-KEY: ".$this->apikey."\r\nContent-type: application/json\r\n",
                "content"=> json_encode($payload)
                )
            );

            $context = stream_context_create($opts);

            $result = file_get_contents('https://payment.safe2pay.com.br/v2/Payment', false, $context);

            if ($result === FALSE) { /* Handle error */ }
            $resultJson = $result;
            $result = json_decode($result);
            
            if($result->HasError){ //caso tenha algum erro
                return [
                    'error' => "Código do Erro: ".$result->ErrorCode." - ".$result->Error,
                    'hasError' => $result->HasError
                ];
            }
            
            if($result->ResponseDetail->Message == "Pagamento Recusado"){ //caso especial, tem q ver os outros
                return [
                    'error' => "Pagamento Recusado - ".$result->ResponseDetail->Description,
                    'hasError' => TRUE
                ];
            }

            return [
                'transaction' => $result->ResponseDetail->IdTransaction,
                'status' => $result->ResponseDetail->Status == 3 ? 'Ativa' : $result->ResponseDetail->Message,
                'resultJson' => $resultJson,
                'hasError' => $result->HasError
            ];

        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return [ 'error' => $e->getMessage() ];
        } 
    }

    public function storeShopSubscription($shop, $request){
        

        try {

            if($shop->token_card){ //caso tenha o token do cartão
                $payload = array(
                    'Plan' => $request->plan_id,
                    'PaymentMethod' => '2', //cartão de crédito
                    "IsSandbox" => $this->sandbox, //retirar isso aqui depois quando for pra produção
                    "Customer" => [
                        "Name" => $request->name,
                        "Identity" => $request->document,
                        "Phone" => $request->phone,
                        "Email" => $request->email,
                        // "Address" => [
                        //     "Street" => $request->street,
                        //     "Number" => $request->number,
                        //     "District" => $request->district,
                        //     "ZipCode" => $request->zipcode,
                        //     "CityName" => $request->city,
                        //     "StateInitials" => $request->state_code,
                        //     "CountryName" => $request->country
                        // ]
                        "Address" => $this->enderecoCobrancaMawa
                    ],
                    "IsSendEmail" => true,
                    "Emails" => [
                        $request->email
                    ],
                    "Token" => $shop->token_card->token
                );
            }else{
                $payload = array(
                    'Plan' => $request->plan_id,
                    'PaymentMethod' => '2', //cartão de crédito
                    "IsSandbox" => $this->sandbox, //retirar isso aqui depois quando for pra produção
                    "Customer" => [
                        "Name" => $request->name,
                        "Identity" => $request->document,
                        "Phone" => $request->phone,
                        "Email" => $request->email,
                        // "Address" => [
                        //     "Street" => $request->street,
                        //     "Number" => $request->number,
                        //     "District" => $request->district,
                        //     "ZipCode" => $request->zipcode,
                        //     "CityName" => $request->city,
                        //     "StateInitials" => $request->state_code,
                        //     "CountryName" => $request->country
                        // ]
                        "Address" => $this->enderecoCobrancaMawa
                    ],
                    "IsSendEmail" => true,
                    "Emails" => [
                        $request->email
                    ],
                    "CreditCard" => [
                        "Holder" => $request->holder,
                        "CardNumber" => $request->card_number,
                        "ExpirationDate" => $request->expiration_date,
                        "SecurityCode" => $request->security_code,
                        "InstallmentQuantity" => 01 //quantidade de parcelas
                    ]
                );
            }
            
            
            //dd($payload);
            $opts = array(
            "http"=>array(
                "method"=>"POST",
                "header"=>"X-API-KEY: ".$this->apikey."\r\nContent-type: application/json\r\n",
                "content"=> json_encode($payload)
                )
            );
            
            $context = stream_context_create($opts);
    
            $result = file_get_contents('https://api.safe2pay.com.br/v2/Subscription/Add', false, $context);
    
            if ($result === FALSE) { 
                return NULL;    
            }
    
            $result = json_decode($result);
    
            return [
                'subscription' => $result->ResponseDetail->Subscription,
                'transaction' => $result->ResponseDetail->Transaction,
                'status' => $result->ResponseDetail->SubscriptionStatus == 1 ? 'Ativa' : $result->ResponseDetail->SubscriptionStatus
            ];
        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return NULL;
        }  
    }

    public function getShopSubscription($subscriptionId){
        //carrega os dados de adesão de um lojista

        try {
            $opts = array(
                'http'=>array(
                  'method'=>"GET",
                  'header'=>"X-API-KEY: ".$this->apikey
                )
              );
              
              $context = stream_context_create($opts);
              
              $result = file_get_contents('https://api.safe2pay.com.br/v2/Subscription/Get?id='.$subscriptionId, false, $context);
              
              if ($result === FALSE) { /* Handle error */ }
            
              $plano = json_decode($result);
    
              return $plano->ResponseDetail;
        } catch (\Exception $e) {
            //report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return NULL;
        }
        
    }

    public function updateShopSubscription($shop){
        //verifica e atualiza o status do plano do usuário passado

        try {
            $opts = array(
                'http'=>array(
                  'method'=>"GET",
                  'header'=>"X-API-KEY: ".$this->apikey
                )
              );
              
              $context = stream_context_create($opts);
              
              $result = file_get_contents('https://api.safe2pay.com.br/v2/Subscription/Get?id='.$shop->contracted_plan->subscription, false, $context);
              
              if ($result === FALSE) { /* Handle error */ }
            
              $plano = json_decode($result);
    
              //atualiza o status do plano contratado pelo usuário
              $shop->contracted_plan->subscription_status = $plano->ResponseDetail->SubscriptionStatus->Name;
              $shop->contracted_plan->save();
    
              return $plano->ResponseDetail;
        } catch (\Exception $e) {
            //report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return NULL;
        }
    }

    public function cancelShopSubscription($shop){
        if(isset($shop->contracted_plan) && isset($shop->contracted_plan->subscription)){
            $curl = curl_init();
            //dd("https://api.safe2pay.com.br/v2/Subscription/Delete?id=".$shop->contracted_plan->subscription);
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.safe2pay.com.br/v2/Subscription/Delete?id=".$shop->contracted_plan->subscription,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => array(
                    "x-api-key" => $this->apikey
                ),
            ));

            $response = curl_exec($curl);

            $err = curl_error($curl);

            //curl_close($curl);
            
            //dd($response);

            if ($err) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getSupplierPlans(){
        $opts = array(
            'http'=>array(
              'method'=>"GET",
              'header'=>"X-API-KEY: ".$this->apikey
            )
          );
          
          $context = stream_context_create($opts);
          
          $result = file_get_contents('https://api.safe2pay.com.br/v2/Plan/List?Object.IsEnabled=true&PageNumber=1&RowsPerPage=100', false, $context);
          
          if ($result === FALSE) { /* Handle error */ }
        
          $planos = json_decode($result);

          return $planos->ResponseDetail->Objects;
    }

    public function getSupplierPlan($planId){
        try {
            //pega os dados de um plano
            $opts = array(
                'http'=>array(
                'method'=>"GET",
                'header'=>"X-API-KEY: ".$this->apikey
                )
            );
            
            $context = stream_context_create($opts);
            
            $result = file_get_contents('https://api.safe2pay.com.br/v2/Plan/Get?id='.$planId, false, $context);
            
            if ($result === FALSE) { /* Handle error */ }
            
            $plano = json_decode($result);

            return $plano->ResponseDetail;
        } catch (\Exception $e) {
            //report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return NULL;
        }
        
    }

    public function storeSupplierSubscription($request){        
        $payload = array(
            'Plan' => $request->plan_id,
            'PaymentMethod' => '2', //cartão de crédito
            "IsSandbox" => $this->sandbox, //retirar isso aqui depois quando for pra produção
            "Customer" => [
                "Name" => $request->name,
                "Identity" => $request->document,
                "Phone" => $request->phone,
                "Email" => $request->email,
                // "Address" => [
                //     "Street" => $request->street,
                //     "Number" => $request->number,
                //     "District" => $request->district,
                //     "ZipCode" => $request->zipcode,
                //     "CityName" => $request->city,
                //     "StateInitials" => $request->state_code,
                //     "CountryName" => $request->country
                // ]
                "Address" => $this->enderecoCobrancaMawa
            ],
            "IsSendEmail" => true,
            "Emails" => [
                $request->email
            ],
            "CreditCard" => [
                "Holder" => $request->holder,
                "CardNumber" => $request->card_number,
                "ExpirationDate" => $request->expiration_date,
                "SecurityCode" => $request->security_code,
                "InstallmentQuantity" => 01 //quantidade de parcelas
            ],
        );

        $opts = array(
        'http'=>array(
            'method'=>"POST",
            'header'=>"X-API-KEY: ".$this->apikey."\r\n" .
                    "Content-type: application/json\r\n",
            'content'=> json_encode($payload)
            )
        );
        
        $context = stream_context_create($opts);

        $result = file_get_contents('https://api.safe2pay.com.br/v2/Subscription/Add', false, $context);

        if ($result === FALSE) { 
            return NULL;    
        }

        $result = json_decode($result);

        return [
            'subscription' => $result->ResponseDetail->Subscription,
            'transaction' => $result->ResponseDetail->Transaction,
            'status' => $result->ResponseDetail->SubscriptionStatus == 1 ? 'Ativa' : $result->ResponseDetail->SubscriptionStatus
        ];
    }

    public function getSupplierSubscription($subscriptionId){
        //carrega os dados de adesão de um fornecedor

        try {
            $opts = array(
                'http'=>array(
                  'method'=>"GET",
                  'header'=>"X-API-KEY: ".$this->apikey
                )
              );
              
              $context = stream_context_create($opts);
              
              $result = file_get_contents('https://api.safe2pay.com.br/v2/Subscription/Get?id='.$subscriptionId, false, $context);
              
              if ($result === FALSE) { /* Handle error */ }
            
              $plano = json_decode($result);
    
              return $plano->ResponseDetail;
        } catch (\Exception $e) {
            //report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return NULL;
        }
        
    }

    public function updateSupplierSubscription($supplier){
        //verifica e atualiza o status do plano do usuário passado

        try {
            $opts = array(
                'http'=>array(
                  'method'=>"GET",
                  'header'=>"X-API-KEY: ".$this->apikey
                )
              );
              
              $context = stream_context_create($opts);
              
              $result = file_get_contents('https://api.safe2pay.com.br/v2/Subscription/Get?id='.$supplier->contracted_plan->subscription, false, $context);
              
              if ($result === FALSE) { /* Handle error */ }
            
              $plano = json_decode($result);
    
              //atualiza o status do plano contratado pelo usuário
              $supplier->contracted_plan->subscription_status = $plano->ResponseDetail->SubscriptionStatus->Name;
              $supplier->contracted_plan->save();
    
              return $plano->ResponseDetail;
        } catch (\Exception $e) {
            //report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return NULL;
        }
    }

    public function cancelSupplierSubscription($supplier){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.safe2pay.com.br/v2/Subscription/Delete?id=".$supplier->contracted_plan->subscription,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "x-api-key" => $this->apikey
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
}