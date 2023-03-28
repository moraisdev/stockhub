<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Services\Shop\ProductsService;

use Auth;

class FreightSimulatorController extends Controller
{
    public function index(){
        $shop = Auth::guard('shop')->user();

        //carrega todos os produtos do lojista
        $productsService = new ProductsService($shop);

        $products = $productsService->paginate(99999);
        
        return view('shop.freight-simulator.index', compact('products'));
    }
}
