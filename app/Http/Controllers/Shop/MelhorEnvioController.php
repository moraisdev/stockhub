<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Services\Shop\ProductsService;

use Auth;

use App\Services\MelhorEnvioService;
use Illuminate\Support\Facades\Log;

class MelhorEnvioController extends Controller
{
    public function simulate(Request $request){
        $shop = Auth::guard('shop')->user();

        $idProduto = $request->product;
        $toZipcode = $request->to_zipcode;

        if($idProduto && $toZipcode){
            try {
                $productsService = new ProductsService($shop);
            
                $product = $productsService->find($idProduto);
                if($product){
                    //pega os dados do supplier do produto
                    $supplier = $product->supplier;

                    if($supplier->shipping_method == 'melhor_envio'){
                        //pega a primeira variante daquele produto
                        $variant = $product->variants[0];
        
                        //caso queiram orçar mais variantes no futuro, por enquanto orça só uma de cada vez
                        $variantsSimulate = [];
        
                        array_push($variantsSimulate, $variant);

                        $melhorEnvioService = new MelhorEnvioService();
                        $melhorEnvioService->setFromZipcode($supplier->address->zipcode);
                        $melhorEnvioService->setToZipcode($toZipcode);
                        $melhorEnvioService->prepareSimulateProducts($variantsSimulate);
                        return $melhorEnvioService->quoteFreight(); //retorna a cotação
                       
                    }else{
                        return 'O fornecedor atualmente não utiliza a Melhor Envio';
                    }
                }
            } catch (\Exception $e) {
                //report($e);
                Log::error('simulate error.', [$e]); //só salva o logo em vez de mostrar no slack
                dd($e);
                return 'Erro ao fazer simulação, tente novamente mais tarde.';
            }
        }        
    }
}
