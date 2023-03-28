<?php

namespace App\Http\Controllers\Shop;

use App\Services\Shop\CatalogService;
use Illuminate\Http\Request;

use App\Models\Suppliers;

use App\Services\PartnersService;
use App\Services\Shop\ProductsService;

use Auth;

class PartnersController extends Controller
{
    public function index(){
    	return view('shop.partners.index');
    }

    public function show($hash){
    	$shop = Auth::guard('shop')->user();

        $supplier = Suppliers::where('hash', $hash)->first();

    	if(!$supplier || !in_array($supplier->id, $shop->suppliers->pluck('id')->toArray())){
    		return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
    	}

    	return view('shop.partners.show', compact('supplier'));
    }

    public function products($slug, $private_hash){
        $shop = Auth::guard('shop')->user();
        $supplier = Suppliers::where('private_hash', $private_hash)->first();

        $productsService = new ProductsService($shop);
        $products = $productsService->getSupplierProductsPage($supplier);
        $categories = CatalogService::getProductCategories($shop);

        return view('shop.catalog.index', compact('supplier', 'products', 'categories'));
    }
}
