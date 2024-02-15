<?php

namespace App\Services\Shop;

use App\Models\Categories;
use App\Models\Products;

class CatalogService{
	public static function getPublicProducts($shop){
		return Products::with('variants', 'supplier')
        ->whereHas('supplier', function($q){
            $q->where('status', 'active')->where('login_status', 'authorized');
        })->where('public', 1)->get();
}

	public static function getProductCategories($shop){
		return Categories::whereHas('products', function($q){
			$q->where('public', 1);
		})->get();
}
}