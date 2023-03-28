<?php

namespace App\Http\Controllers\Shop;
use Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopProducts;
use App\Models\Products;
use App\Services\BlingService;
use App\Models\ProductVariants;
use App\Models\ProductVariantStock;
use App\Models\ProductImages;

class BlingController extends Controller

{




    public function exportBlingJson(Request $request){

        $shop = Auth::guard('shop')->user();

       $produtos = ProductVariants::whereIn('product_id', $shop->products->pluck('id'))->get();
       $stock = ProductVariantStock::whereIn('product_variant_id', $shop->products->pluck('id'))->get();
       $imagens = ProductImages::where('product_id', $shop->products->pluck('id'))->get();

     //  dd($produtos);
       if ($shop->bling_apikey) {

        foreach ($produtos as $prod) {
            if ($prod){
            $stock = ProductVariantStock::where('product_variant_id', $prod->id)->first();
            $blingService = new BlingService();
             $imagens = ProductImages::where('product_id', $prod->product_id)->get();
             $produtosBling = $blingService->exportProducts($shop, $prod , $stock, $imagens);
            }
			//dd($produtosBling);

        }
       }
		

		
       echo 'Exportação OK';



    }
}
