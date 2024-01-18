<?php
namespace App\Services;

use App\Models\SupplierOrders;
use App\Models\Suppliers;
use App\Models\AdminMelhorEnvioSettings ;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Log;
use App\Models\ErrorLogs;
use GuzzleHttp\Client;

class CitelService{

    public static function getPaidOrders($codclientecitel, $orderitems , $order ){        

    try {   
       $client = new Client();
       $numeroPocket = substr(uniqid(), -9);
       

       $dataitens['itens'] = [];

        foreach ($orderitems as $orderitem) {
           $total = $orderitem->amount * $orderitem->quantity;  
          $item = [
        "produto" => $orderitem->sku,
        "precoUnitario" => $orderitem->amount,
        "quantidade" => $orderitem->quantity,
        "totalItem" => $total
        ];

            $dataitens['itens'][] = $item;
         }

       
      
      $data = [
        "cliente" => $codclientecitel,
        "codigoDigitador" => "001",
        "codigoVendedor" => "001",
        "condicaoPagamento" => "001",
        "especieDocumento" => "PD",
        "formaPagamento" => "001",
        "objCondicaoPagamento" => "001",
        "itens" => array_merge($dataitens['itens']),
        "numeroPocket" => $numeroPocket,
        "totalProdutos" => $order->items_amount,
        "valorContabil" => $order->items_amount
    ];
    
       $response = $client->request('POST', 'http://144.22.159.9:7031/pedidovenda', [
        'auth' => ['TESTE', '123'],
        'json' => $data
    ]);
    
    $body = $response->getBody();
    $data = json_decode($body, true);
    return ['status' => '200', 'resposta' => $data]; 
    

} catch (\Exception $e) {
    Log::error($e);
    ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        return ['status' => '404', 'resposta' => $e->getCode()];  
    
    
}
    }


   




   

     public static function getCadCliente($customers , $customersandress){

        try {      
      $cliente = [
          "bairro" => $customersandress->address2, // campo obrigatorio 
          "cep" => $customersandress->zipcode, // campo obrigatorio
          "cepCobranca" => $customersandress->zipcode, 
          "codigoAtividade" => "009", // campo obrigatorio
          "codigoVendedor" => "109", // campo obrigatorio         
          "emailNfe" => $customers->email, // campo obrigatorio
          "endereco" => $customersandress->address1, // campo obrigatorio
          "nome" => $customers->first_name .''. $customers->last_name, // campo obrigatorio
          "numero" => $customersandress->number, // campo obrigatorio
          "numeroDocumento" => $customers->cpf, // campo obrigatorio
          "tabelaPreco" => "1",  
          "tipoDocumento" => 2
      ];


        

      $client = new Client();
      $response = $client->request('POST', 'http://144.22.159.9:7031/cliente', [
        'auth' => ['TESTE', '123'],
        'json' => $cliente
    ]);
    
    $body = $response->getBody();
    $cliente = json_decode($body, true);
    return ['status' => '200', 'resposta' => $cliente];  

} catch (\Exception $e) {
        Log::error($e);
        ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        return ['status' => '404', 'resposta' => $e];  
    
    // Tratamento de erro adicional, se necessário
    // ...
}
    }

     public static function getConsCliente($cpf){

        try {       
  
      $client = new Client();
      $response = $client->request('GET', 'http://144.22.159.9:7031/cliente/'.$cpf, [
        'auth' => ['TESTE', '123'],
        
    ]);
    
   
    $body = json_decode($response->getBody());
    
    return  ['status' => '200', 'resposta' => $body];  

} catch (\Exception $e) {
    Log::error($e);
    ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        return ['status' => '404', 'resposta' => $e->getCode()];  
        
    // Tratamento de erro adicional, se necessário
    // ...
}
    }
}    

   