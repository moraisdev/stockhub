<?php

namespace App\Http\Controllers\Api;

use App\Models\Orders;
use Illuminate\Http\Request;
use Carbon\Carbon;


use App\Models\WebhookCalls;
use App\Models\ProductVariantStock;
use Illuminate\Support\Facades\Log;
use App\Models\ProductImages;
use App\Services\BlingService;
use App\Models\Products;
use App\Services\Shop\CsvService;
use App\Models\ProductVariants;
use App\Models\Suppliers;
use App\Models\Shops;
use App\Services\CitelService;
use App\Models\Customers;
use App\Models\CustomerAddresses;
use App\Models\OrderItems;


use App\Models\ShopContractedPlans;

class BlingController extends Controller
{
    public function updateStock(Request $request){
        $sku = '';
        try {
            $response = json_decode($request->data)->retorno->estoques[0]->estoque;
            
            $sku = $response->codigo;

            $variant = ProductVariants::where('sku', $sku)->first();
            $stock = ProductVariantStock::where('product_variant_id', $variant->id)->first();
            $stock->quantity = $response->estoqueAtual;
            

            if($stock->save()){
                return response()->json([
                    'success' => 'Atualizado com sucesso.'
                ], 200); 
            }else{
                return response()->json([
                    'error' => 'Erro ao atualizar.'
                ], 400); 
            }

        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar estoque com o bling, sku '.$sku);
            Log::error($e);
        }
        //verifica se o sku do produto existe na mawa

        //caso exista, atualiza o estoque de acordo com o estoque que veio do bling
        WebhookCalls::create([
            'identifier' => 'bling.update-stock',
            'content' => json_encode($request->all())
         ]);
    }


    public function valida_img(){

        $product = new Products();
        $respimg3 = $product->get();
        $productimg =  ProductImages::all();
        
        
    
        foreach ($productimg as $img){
            $validacao = CsvService::validarext($img->src);
            if($validacao == false){
               
              
                  $img->delete();
            }
             

        }    
            $suppliers = Suppliers::get();
            

            foreach ($suppliers as $sup){
                if ($sup->bling_apikey){
                    if (Products::where('supplier_id', $sup->id)->count() <> 0) {
                        $Products = Products::where('supplier_id', $sup->id)->get();
                        if($Products){
                            foreach ($Products as $prod) {
                                $url = $prod->img_source;
                                $validacao = CsvService::validarext($url);
                                if ($validacao == false) {
                                    $blingService = new BlingService();
                                    $produtosBling = $blingService->importProductsid($sup, $prod->sku);
                                   
                                    if (isset($produtosBling[0]->produto->imagem)) {
                                        foreach ($produtosBling[0]->produto->imagem as $key => $link) {
                                            if ($link && property_exists($link, 'link')) {
                                               
                                                $productimg = new Products();
                                                $respimg = $productimg->where('sku', $produtosBling[0]->produto->codigo)->where('supplier_id', $sup->id)->first();
                                                $respimg->img_source = $link->link;
                                                $respimg->save();
                                                $prodvarimg = new ProductVariants();
                                                $respimg2 = $prodvarimg->where('sku', $produtosBling[0]->produto->codigo)->where('product_id', $respimg->id)->first();
                                                $respimg2->img_source = $link->link;
                                                $respimg2->save();                                          
                                               }   


                                    $product = new Products();
                                    $respimg3 = $product->where('sku', $produtosBling[0]->produto->codigo)->where('id', $sup->id)->first();
                             
                                    if($respimg3){		
                                    if (isset($produtosBling[0]->produto->imagem)) {
                                        foreach ($produtosBling[0]->produto->imagem as $key => $link) { 
                                            $verificaimagem = ProductImages::where('img_bling', $link->link)->first();
                                                if (!$verificaimagem) {                                                    
                                                ProductImages::create([
                                                    'product_id' => $respimg3->id,
                                                    'title' => $link->link,
                                                    'src'=> $link->link,
                                                    'img_bling'=> $link->link,
                                                    'img_bling_validade' => $link->validade,

                                                ]);

                                            }
                                        }    

                                    }
                                }


                                        
                                        
                                        } 
                               
                                    }
                            
                                }   
                         
                            }  
                        }


                }
            }    


            }    

                                        




        }



        public function planofornecedor(){

         $shopplans = ShopContractedPlans::get();
         
         foreach ($shopplans as $planos)  {

            date_default_timezone_set("America/Sao_Paulo");
            $dataat = date("Y-m-d");
            if($planos->due_date <= $dataat){
                $planos->subscription_status = 'inactive';
                $planos->save();
                $shop = Shops::where('id' , $planos->shop_id)->first();
                if($shop){
                    $shop->status = 'inactive';
                    $shop->save();
        
                }
                
            }
            


         }



        }   

        
        public function citel(){

            $id = 66;
            
            $order = Orders::where('id' ,$id )->first();
            $customers = Customers::where('id' ,$order->customer_id )->first();
            $customersandress = CustomerAddresses::where('customer_id', $customers->id)->first();

            $orderitems = OrderItems::where('order_id' ,$id )->get();
            
            $consultaclientecitel = CitelService::getConsCliente($customers->cpf);
            
            if($consultaclientecitel['status'] == '200'){

               $dadoscliente =  $consultaclientecitel['resposta'];
               
               $codclientecitel = $dadoscliente->codigoCliente;
               $ordercitel = CitelService::getPaidOrders($codclientecitel, $orderitems, $order );
               
            }else {
                $cadclientecitel = CitelService::getCadCliente($customers , $customersandress );
                
                 if ($cadclientecitel['status'] == '200'){
                    $dadoscliente =  $consultaclientecitel['resposta'];
               
                    $codclientecitel = $dadoscliente->codigoCliente;
                    $ordercitel = CitelService::getPaidOrders( $codclientecitel, $orderitems, $order );
                    

                 }
                    
            }
            
           
        }    

}
