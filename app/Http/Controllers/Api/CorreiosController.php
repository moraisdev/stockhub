<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariants;
use App\Services\CorreiosService;
use Illuminate\Http\Request;

class CorreiosController extends Controller
{
    public function simulate(Request $request, CorreiosService $service){
        $service->setFromZipcode($request->from_zipcode);
        $service->setToZipcode($request->to_zipcode);

        $variant = ProductVariants::find($request->product_id);

        if(!$variant){
            return ['status' => 'error', 'message' => 'Este produto é inválido.'];
        }

        $products = [[
            'width' => $variant->width,
            'height' => $variant->height,
            'depth' => $variant->depth,
            'weight' => $variant->weight_in_grams,
            'qty' => 1
        ]];

        $service->calcBoxSize($products);

        $prices = $service->getShippingPrices($variant->product->supplier);

        if($prices){
            return ['status' => 'success', 'message' => 'Simulação realizada com sucesso', 'data' => $service->getShippingPrices($variant->product->supplier)];
        }else{
            return ['status' => 'error', 'message' => 'Houve uma instabilidade na API dos correios, tente novamente em alguns minutos.'];
        }
    }
}
