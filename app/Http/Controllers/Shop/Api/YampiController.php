<?php

namespace App\Http\Controllers\Shop\Api;

use App\Models\Shops;
use App\Services\Shop\YampiService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class YampiController extends Controller
{
    public function __construct(YampiService $yampiService)
    {
        $this->yampiService = $yampiService;
    }

    public function getPaidOrders()
    {
        // $shop = Auth::guard('shop')->user();
        $shop = Shops::Where('id', 349)->first();

        $result = $this->yampiService->getPaidOrders($shop);

        return $result;
    }

    public function getBrands()
    {
        // $shop = Auth::guard('shop')->user();
        $shop = Shops::Where('id', 349)->first();

        $result = $this->yampiService->getBrands($shop);

        return $result;
    }

    public function importOrders()
    {
        // $shop = Auth::guard('shop')->user();
        $shop = Shops::Where('id', 349)->first();

        $result = $this->yampiService->getPaidOrders($shop);

        if ($result['status'] == 'success') {
            foreach ($result['data'] as $yamp_order) {
               return $this->yampiService->registerOrder($shop, $yamp_order);
            }
        } else {
            return ['status' => 'error', 'message' => 'tente novamente em alguns minutos.'];
        }

        return ['status' => 'success', 'message' => 'Pedido importado com sucesso'];
    }


    public function updatedTracking(Request $request, $id)
    {
        $result = $this->yampiService->updatedTracking($request, $id);

        if ($result) {
            return ['status' => 'success', 'message' => 'Tracking atualizado', 'data' => $result];
        } else {
            return ['status' => 'error', 'message' => 'tente novamente em alguns minutos.'];
        }
    }
    public function exportYampiJson(Request $request){
        
        try {
            // $shop_domain = 'edu-devstore';
            // $shop = Shops::whereHas('shopify_app', function($q) use ($shop_domain){
            //     $q->where('domain', $shop_domain);
            // })->first();
            $shop = Shops::Where('id', 349)->first();
            // $shop = Auth::guard('shop')->user();
 
            $productsService = new ProductsService($shop);
            $product = $productsService->find($request->product_id);
    
            $yampi_product = YampiService::registerProductJson($shop, $product);
    
            if($yampi_product){
                //ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['exported' => 1]);
                return response()->json([
                    'success' => 'Produto exportado para o yampi com sucesso. Lembre-se de corrigir os valores do produto antes de publica-lo em sua loja.',
                    'product_id' => $product->id,
                    'yampi_product' => $yampi_product], 200);
            }else{
                return response()->json(['error' => 'Aconteceu um erro inesperado. Tente novamente em alguns minutos.', $yampi_product], 400);
            }
        } catch (\Throwable $th) {
           return response()->json($th->getMessage());
        }
    }
    public function exportImagesProductYampiJson(Request $request){
        //set_time_limit(120);
        $shop = Auth::guard('shop')->user();

        $productsService = new ProductsService($shop);
        $product = $productsService->find($request->product_id);
        
        if(YampiService::registerImagesProductJson($shop, $request->yampi_product, $product)){
            //ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['exported' => 1]);
            return response()->json([ 'success' => 'Imagens exportadas com sucesso.'], 200);
        }else{
            return response()->json(['error' => 'Erro ao exportar imagens.'], 400);
        }
    }
}
