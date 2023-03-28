<?php

namespace App\Services\Shop;

use App\Models\Categories;
use App\Models\Products;

class CatalogService{
	public static function getPublicProducts($shop){
		if($shop->products){
			$products_ids = $shop->products->pluck('id')->toArray();
		}else{
			$products_ids = [];
		}

		return Products::with('variants', 'supplier')->whereHas('supplier', function($q){
		    $q->where('status', 'active')->where('login_status', 'authorized');
        })->where('public', 1)->whereNotIn('id', $products_ids)->get();
	}

	public static function getProductCategories($shop){
        if($shop->products){
            $products_ids = $shop->products->pluck('id')->toArray();
        }else{
            $products_ids = [];
        }

	    return Categories::whereHas('products', function($q) use ($products_ids){
	        $q->where('public', 1)->whereNotIn('id', $products_ids);
        })->get();
    }
}
