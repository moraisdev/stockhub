<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
	use SoftDeletes;

    protected $guarded = [];
    protected $table = 'products';
    public $timestamps = true;

    public function supplier(){
        return $this->belongsTo(Suppliers::class, 'supplier_id')->with('address');
    }

    public function variants(){
    	return $this->hasMany(ProductVariants::class, 'product_id')->with('options_values', 'inventory');
    }

    public function discounts(){
    	return $this->hasMany(ProductDiscounts::class, 'product_id');
    }

    public function published_variants(){
        return $this->hasMany(ProductVariants::class, 'product_id')->where('published', 1)->with('options_values', 'inventory');
    }

    public function options(){
    	return $this->hasMany(ProductOptions::class, 'product_id');
    }

    public function pivots(){
        return $this->hasMany(ShopProducts::class, 'product_id' );
    }

    public function shop_prod(){
        return $this->hasMany(ShopProducts::class, 'shop_id');
    }
    public function shop_product(){
        return $this->hasMany(ShopProducts::class, 'product_id'  );
    }

    public function shop_pivot($shop_id){
        return $this->pivots->where('shop_id', $shop_id)->first();
    }

    public function category(){
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function images(){
        return $this->hasMany(ProductImages::class, 'product_id');
    }

    public function produto(){

       // return $this->
    }
}
