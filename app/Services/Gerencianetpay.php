<?php

namespace App\Services;


use Gerencianet\Exception\GerencianetException;
use Gerencianet\Gerencianet;
use App\Models\SupplierOrderGroup;
use App\Models\SupplierOrders;

class Gerencianetpay{

    public function pay($supplier , $group , $metododepg , $orders , $shop){
      

        $cert = "certsger/".$supplier->geren_pem;
        $credenciais = array(
            'client_id' => $supplier->geren_cliente_id,
            'client_secret' => $supplier->geren_cliente_se,
            'pix_cert' => $cert,
            'sandbox'=> false,
            'timeout'=> 30
        );

     
       $file = file_get_contents(public_path('certsger/config.json'));
       $credenciais = json_encode($credenciais);
       $options = json_decode($credenciais, true);
       
      //dd($shop);


      $cpfcnpj =  mb_strlen($shop->document);
     
      $totalamotx = round($orders->total_amount + ($orders->total_amount / 100 * 1.192), 2) +  0.01;
      //dd($totalamotx);
      //(round($countPixValue * 0.01, 2) + 0.01);
      if ($cpfcnpj <= 13){
        
        $body = [
            "calendario" => [
                "expiracao" => 86400
            ],
            "devedor" => [
                "cpf" => (string) $shop->document,
                "nome" => (string) $shop->responsible_name
            ],
            "valor" => [
                "original" => (string) $totalamotx
            ],
            "chave" => (string) $supplier->geren_chave, // Chave pix da conta Gerencianet do recebedor
            "solicitacaoPagador" => (string )$orders->order_id,

          
            "infoAdicionais" => [
                [
                    "nome" => (string) $supplier->name, // Nome do campo string (Nome) ≤ 50 characters
                    "valor" => "Recebedor" // Dados do campo string (Valor) ≤ 200 characters
                ],
               
            ]
            
        ];


      } else {
        
        $body = [
            "calendario" => [
                "expiracao" => 86400
            ],
            "devedor" => [
                "cnpj" => (string) $shop->document,
                "nome" => (string) $shop->corporate_name
            ],
            "valor" => [
                "original" => (string ) $totalamotx
            ],
            "chave" => (string) $supplier->geren_chave, // Chave pix da conta Gerencianet do recebedor
            "solicitacaoPagador" => (string )$orders->order_id,
            "infoAdicionais" => [
                [
                    "nome" => $supplier->name, // Nome do campo string (Nome) ≤ 50 characters
                    "valor" => "Recebedor" // Dados do campo string (Valor) ≤ 200 characters
                ],
                
            ]
        ];

      }
        
        
        
        try {
            $api = Gerencianet::getInstance($options);
            $pix = $api->pixCreateImmediateCharge([], $body);
            
        
            if ($pix['txid']) {
                $params = [
                    'id' => $pix['loc']['id']
                ];
        
                // Gera QRCode
                $qrcode = $api->pixGenerateQRCode($params);
            
            }  

          //  $supperorder = SupplierOrderGroup:all();

          $result = json_encode($qrcode);
          $group->qrcode_pix = $qrcode['imagemQrcode'] ;
          $group->key_pix = $qrcode['qrcode'] ;
          $group->transaction_id_pix = $pix['txid'] ;
          $group->paid_by = 'pix';
          $group->payment_json_pix = json_encode($pix);
          $group->status_pix = '1';
          $group->description_pix = 'Estamos aguardando a transferência do valor. Após efetuada, o pagamento pode levar até 5 minutos para ser compensado.';
          $group->message_pix = 'Pagamento Pendente';
          $group->save();


          return ['status' => 'success', 'message' => 'Pix gerado com sucesso.'];
   

            

        } catch (GerencianetException $e) {
            print_r($e->code);
            print_r($e->error);
            print_r($e->errorDescription);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }    

    public function payboleto($supplier , $group , $metododepg , $orders , $shop){

        
        $caminho = getenv('APP_URL').'/api/gerencianet/webhooks/orders/paid';
         
        $cert = "certsger/".$supplier->geren_pem;
    
        $credenciais = array(
            'client_id' => $supplier->geren_cliente_id,
            'client_secret' => $supplier->geren_cliente_se,
            'pix_cert' => $cert,
            'sandbox'=> false,
            'timeout'=> 30
        );

     
       $credenciais = json_encode($credenciais);
       $options = json_decode($credenciais, true);
       unset($options['pix_cert']);      
    
        $item_1 = [
            'name' => "Pedido Order Nº".$orders->id,
            'amount' => 1,
            'value' => (int)  number_format($orders->total_amount, 2, '', '') + 345
        ];
    
        $items = [
            $item_1
        ];
    
        $customer = [
            'name' => (string) $shop->name,
            'cpf' => (string) $shop->document,
            'phone_number' => (string) $shop->phone
        ];
    
        $bankingBillet = [
            'expire_at' => date('Y-m-d', strtotime('+1 days')),
            'customer' => $customer
        ];
        $payment = ['banking_billet' => $bankingBillet];
   //     $metadata = array('notification_url'=> $caminho); 
    
        $body = [
            'items' => $items,
            'payment' => $payment,
         //   'metadata' =>$metadata
        ];
    
      
        try {
            $api = new Gerencianet($options);
            $pay_charge = $api->oneStep([], $body);
    
         //   echo json_encode($pay_charge);
        
          $group->transaction_id = $pay_charge['data']['charge_id'];
         
          $group->bankslip_url = $pay_charge['data']['link'] ;
          $group->bankslip_digitable_line = $pay_charge['data']['barcode'];
          $group->bankslip_barcode = $pay_charge['data']['barcode'];
          $group->bankslip_duedate = $pay_charge['data']['expire_at'];
          $group->paid_by = 'boleto';
          $group->save();

          return ['status' => 'success', 'message' => 'Boleto gerado com sucesso.'];
           
          
            

        } catch (GerencianetException $e) {
          //  print_r($e->code);
          //  print_r($e->error);
          //  print_r($e->errorDescription);
          return ['status' => 'erro', 'message' => 'Erro ao gerar boleto.'];
        } catch (Exception $e) {
           // print_r($e->getMessage());
           return ['status' => 'erro', 'message' => 'Erro ao gerar boleto.'];
        }
   
    }


    public function consultapix($supplier , $group , $orders , $shop){

        //  dd($orders);   
         $cert = "certsger/".$supplier->geren_pem;
     //   dd($cert);
         $credenciais = array(
             'client_id' => $supplier->geren_cliente_id,
             'client_secret' => $supplier->geren_cliente_se,
             'pix_cert' => $cert,
             'sandbox'=> false,
             'timeout'=> 30
         );
 
      
        $credenciais = json_encode($credenciais);
        $options = json_decode($credenciais, true);

       
        
        $params = ['txid' => $group->transaction_id_pix];
       
         try {
            $api = Gerencianet::getInstance($options);
            $pix = $api->pixDetailCharge($params);
       
            return $pix['status'];
            
             
 
         }  catch (GerencianetException $e) {
           // print_r($e->code);
           // print_r($e->error);
           // print_r($e->errorDescription);
           return $e;
           
         } catch (Exception $e) {
            return $e;
         }
    
     }


     public function payplanshoppix($metododepg , $shop_invoice , $shop , $admins){
      

        $cert = "certsger/".$admins->geren_pem;
        $credenciais = array(
            'client_id' => $admins->geren_cliente_id,
            'client_secret' => $admins->geren_cliente_se,
            'pix_cert' => $cert,
            'sandbox'=> false,
            'timeout'=> 30
        );

     
   //    $file = file_get_contents(public_path('certsger/config.json'));
       $credenciais = json_encode($credenciais);
       $options = json_decode($credenciais, true);
       
      //dd($shop);

      $valorplanoshop = number_format($shop_invoice->total, 2, '.','.');

      $cpfcnpj =  mb_strlen($shop->document);
     
      if ($cpfcnpj <= 13){
        
        $body = [
            "calendario" => [
                "expiracao" => 86400
            ],
            "devedor" => [
                "cpf" => (string) $shop->document,
                "nome" => (string) $shop->responsible_name
            ],
            "valor" => [
                "original" => (string) $valorplanoshop
            ],
            "chave" => (string) $admins->geren_chave, // Chave pix da conta Gerencianet do recebedor
            "solicitacaoPagador" => (string )$shop_invoice->id,

          
            "infoAdicionais" => [
                [
                    "nome" => (string) $admins->name, // Nome do campo string (Nome) ≤ 50 characters
                    "valor" => "Recebedor" // Dados do campo string (Valor) ≤ 200 characters
                ],
               
            ]
            
        ];


      } else {
        
        $body = [
            "calendario" => [
                "expiracao" => 86400
            ],
            "devedor" => [
                "cnpj" => (string) $shop->document,
                "nome" => (string) $shop->corporate_name
            ],
            "valor" => [
                "original" => (string ) $valorplanoshop
            ],
            "chave" => (string) $admins->geren_chave, // Chave pix da conta Gerencianet do recebedor
            "solicitacaoPagador" => (string )$shop_invoice->id,
            "infoAdicionais" => [
                [
                    "nome" => $admins->name, // Nome do campo string (Nome) ≤ 50 characters
                    "valor" => "Recebedor" // Dados do campo string (Valor) ≤ 200 characters
                ],
                
            ]
        ];

      }
        
        
        
        try {
            $api = Gerencianet::getInstance($options);
            $pix = $api->pixCreateImmediateCharge([], $body);
            
        
            if ($pix['txid']) {
                $params = [
                    'id' => $pix['loc']['id']
                ];
        
                // Gera QRCode
                $qrcode = $api->pixGenerateQRCode($params);
            
            }  

          //  $supperorder = SupplierOrderGroup:all();

          $result = json_encode($qrcode);
          $shop_invoice->qrcode_pix = $qrcode['imagemQrcode'] ;
          $shop_invoice->key_pix = $qrcode['qrcode'] ;
          $shop_invoice->transaction_id_pix = $pix['txid'] ;
          $shop_invoice->paid_by = 'pix';
          $shop_invoice->payment_json_pix = json_encode($pix);
          $shop_invoice->status_pix = '1';
          $shop_invoice->description_pix = 'Estamos aguardando a transferência do valor. Após efetuada, o pagamento pode levar até 5 minutos para ser compensado.';
          $shop_invoice->message_pix = 'Pagamento Pendente';
          $shop_invoice->payment_bank = 'Gerencianet';
          $shop_invoice->save();


          return ['status' => 'success', 'message' => 'Pix gerado com sucesso.'];
   

            

        } catch (GerencianetException $e) {
            print_r($e->code);
            print_r($e->error);
            print_r($e->errorDescription);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }


    public function payplanshopboleto($metododepg , $shop_invoice , $shop,$admins){

        
        $caminho = getenv('APP_URL').'/api/gerencianet/webhooks/orders/paid';
         
        $cert = "certsger/".$admins->geren_pem;
    
        $credenciais = array(
            'client_id' => $admins->geren_cliente_id,
            'client_secret' => $admins->geren_cliente_se,
            'pix_cert' => $cert,
            'sandbox'=> false,
            'timeout'=> 30
        );

     
       $credenciais = json_encode($credenciais);
       $options = json_decode($credenciais, true);
       unset($options['pix_cert']);      
    
        $item_1 = [
            'name' => "Fatura  Nº".$shop_invoice->id,
            'amount' => 1,
            'value' => (int)  number_format($shop_invoice->total, 2, '', '')
        ];
    
        $items = [
            $item_1
        ];
    
        $customer = [
            'name' => (string) $shop->name,
            'cpf' => (string) $shop->document,
            'phone_number' => (string) $shop->phone
        ];
    
        $bankingBillet = [
            'expire_at' => date('Y-m-d', strtotime('+1 days')),
            'customer' => $customer
        ];
        $payment = ['banking_billet' => $bankingBillet];
   //     $metadata = array('notification_url'=> $caminho); 
    
        $body = [
            'items' => $items,
            'payment' => $payment,
         //   'metadata' =>$metadata
        ];
    
      
        try {
            $api = new Gerencianet($options);
            $pay_charge = $api->oneStep([], $body);
    
         //   echo json_encode($pay_charge);
        
         $shop_invoice->transaction  = $pay_charge['data']['charge_id'];
         
         $shop_invoice->bankslip_url = $pay_charge['data']['link'] ;
         $shop_invoice->bankslip_digitable_line = $pay_charge['data']['barcode'];
         $shop_invoice->bankslip_barcode = $pay_charge['data']['barcode'];
         $shop_invoice->bankslip_duedate = $pay_charge['data']['expire_at'];
         $shop_invoice->paid_by = 'boleto';
         $shop_invoice->payment_bank = 'Gerencianet';
         $shop_invoice->save();

          return ['status' => 'success', 'message' => 'Boleto gerado com sucesso.'];
           
          
            

        } catch (GerencianetException $e) {
          //  print_r($e->code);
          //  print_r($e->error);
          //  print_r($e->errorDescription);
          return ['status' => 'erro', 'message' => 'Erro ao gerar boleto.'];
        } catch (Exception $e) {
           // print_r($e->getMessage());
           return ['status' => 'erro', 'message' => 'Erro ao gerar boleto.'];
        }
   
    }


    public function payplansupplierpix($metododepg , $supplier_invoice , $supplier , $admins){
      

        $cert = "certsger/".$admins->geren_pem;
        $credenciais = array(
            'client_id' => $admins->geren_cliente_id,
            'client_secret' => $admins->geren_cliente_se,
            'pix_cert' => $cert,
            'sandbox'=> false,
            'timeout'=> 30
        );

     
   //    $file = file_get_contents(public_path('certsger/config.json'));
       $credenciais = json_encode($credenciais);
       $options = json_decode($credenciais, true);
       
      //dd($shop);

      $valorplanosupplier = number_format($supplier_invoice->total, 2, '.','.');

        
             
        $body = [
            "calendario" => [
                "expiracao" => 86400
            ],
            "devedor" => [
                "cnpj" => (string) $supplier->document,
                "nome" => (string) $supplier->legal_name
            ],
            "valor" => [
                "original" => (string ) $valorplanosupplier
            ],
            "chave" => (string) $admins->geren_chave, // Chave pix da conta Gerencianet do recebedor
            "solicitacaoPagador" => (string )$supplier_invoice->id,
            "infoAdicionais" => [
                [
                    "nome" => $admins->name, // Nome do campo string (Nome) ≤ 50 characters
                    "valor" => "Recebedor" // Dados do campo string (Valor) ≤ 200 characters
                ],
                
            ]
        ];

      
        
        
        
        try {
            $api = Gerencianet::getInstance($options);
            $pix = $api->pixCreateImmediateCharge([], $body);
            
          
            if ($pix['txid']) {
                $params = [
                    'id' => $pix['loc']['id']
                ];
        
                // Gera QRCode
                $qrcode = $api->pixGenerateQRCode($params);
            
            }  

          //  dd($pix);
          //  $supperorder = SupplierOrderGroup:all();

          $result = json_encode($qrcode);
          $supplier_invoice->qrcode_pix = $qrcode['imagemQrcode'] ;
          $supplier_invoice->key_pix = $qrcode['qrcode'] ;
          $supplier_invoice->transaction_id_pix = $pix['txid'] ;
          $supplier_invoice->paid_by = 'pix';
          $supplier_invoice->payment_json_pix = json_encode($pix);
          $supplier_invoice->status_pix = '1';
          $supplier_invoice->description_pix = 'Estamos aguardando a transferência do valor. Após efetuada, o pagamento pode levar até 5 minutos para ser compensado.';
          $supplier_invoice->message_pix = 'Pagamento Pendente';
          $supplier_invoice->payment_bank = 'Gerencianet';
          $supplier_invoice->save();

          
          return ['status' => 'success', 'message' => 'Pix gerado com sucesso.'];
   

            

        } catch (GerencianetException $e) {
            print_r($e->code);
            print_r($e->error);
            print_r($e->errorDescription);
			
		
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }


    public function payplansupplierpboleto($metododepg , $supplier_invoice , $supplier,$admins){

        
        $caminho = getenv('APP_URL').'/api/gerencianet/webhooks/orders/paid';
         
        $cert = "certsger/".$admins->geren_pem;
    
        $credenciais = array(
            'client_id' => $admins->geren_cliente_id,
            'client_secret' => $admins->geren_cliente_se,
            'pix_cert' => $cert,
            'sandbox'=> false,
            'timeout'=> 30
        );

     
       $credenciais = json_encode($credenciais);
       $options = json_decode($credenciais, true);
       unset($options['pix_cert']);      
    
        $item_1 = [
            'name' => "Fatura  Nº".$supplier_invoice->id,
            'amount' => 1,
            'value' => (int)  number_format($supplier_invoice->total, 2, '', '')
        ];
    
        $items = [
            $item_1
        ];

        $juridical_data = [
            'corporate_name' => (string) $supplier->legal_name, 
            'cnpj' => (string) $supplier->document 
          ];
    
        $customer = [
            'name' => (string) $supplier->legal_name,
            'phone_number' => (string) $supplier->phone,
            'juridical_person' => $juridical_data
        ];
    
        $bankingBillet = [
            'expire_at' => date('Y-m-d', strtotime('+1 days')),
            'customer' => $customer
        ];
        $payment = ['banking_billet' => $bankingBillet];
   //     $metadata = array('notification_url'=> $caminho); 
    
        $body = [
            'items' => $items,
            'payment' => $payment,
         //   'metadata' =>$metadata
        ];
    
      
        try {
            $api = new Gerencianet($options);
            $pay_charge = $api->oneStep([], $body);
    
         //   echo json_encode($pay_charge);
        
         $supplier_invoice->transaction  = $pay_charge['data']['charge_id'];
         
         $supplier_invoice->bankslip_url = $pay_charge['data']['link'] ;
         $supplier_invoice->bankslip_digitable_line = $pay_charge['data']['barcode'];
         $supplier_invoice->bankslip_barcode = $pay_charge['data']['barcode'];
         $supplier_invoice->bankslip_duedate = $pay_charge['data']['expire_at'];
         $supplier_invoice->paid_by = 'boleto';
         $supplier_invoice->payment_bank = 'Gerencianet';
         $supplier_invoice->save();

          return ['status' => 'success', 'message' => 'Boleto gerado com sucesso.'];
           
          
            

        } catch (GerencianetException $e) {
          //  print_r($e->code);
          //  print_r($e->error);
          //  print_r($e->errorDescription);
          return ['status' => 'erro', 'message' => 'Erro ao gerar boleto.'];
        } catch (Exception $e) {
           // print_r($e->getMessage());
           return ['status' => 'erro', 'message' => 'Erro ao gerar boleto.'];
        }
   
    }

    public function consultapixplano($admins , $plan_invoice){

        //  dd($orders);   
         $cert = "certsger/".$admins->geren_pem;
     //   dd($cert);
         $credenciais = array(
             'client_id' => $admins->geren_cliente_id,
             'client_secret' => $admins->geren_cliente_se,
             'pix_cert' => $cert,
             'sandbox'=> false,
             'timeout'=> 30
         );
 
      
        $credenciais = json_encode($credenciais);
        $options = json_decode($credenciais, true);

       
        
        $params = ['txid' => $plan_invoice->transaction_id_pix];
		
       
         try {
            $api = Gerencianet::getInstance($options);
            $pix = $api->pixDetailCharge($params);
       
            return $pix['status'];
            
             
 
        
          } catch (GerencianetException $e) {
        
          return $e;
        } catch (Exception $e) {
          
           return $e;
        }
   
    }



}
