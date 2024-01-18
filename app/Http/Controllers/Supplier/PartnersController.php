<?php

namespace App\Http\Controllers\Supplier;

use Illuminate\Http\Request;

use App\Models\Shops;

use App\Services\PartnersService;

use Auth;

class PartnersController extends Controller
{
    public function index(){
    	return view('supplier.partners.index');
    }

    public function show($shop_hash){
    	$supplier = Auth::user();
    	$shop = Shops::where('hash', $shop_hash)->first();

    	if(!$shop || !in_array($shop->id, $supplier->shops->pluck('id')->toArray())){
    		return redirect()->back()->with('error', 'Aconteceu um erro inesperado. Tente novamente em alguns minutos.');
    	}

    	$orders = PartnersService::getShopOrdersBySupplier($shop, $supplier);

    	return view('supplier.partners.show', compact('shop', 'orders'));
    }
}
