<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopProducts extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'shop_products';
    public $timestamps = true;


    public function product(){
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function shop(){
        return $this->belongsTo(Shops::class, 'shop_id');
    }

    public function variants(){
    	return $this->hasMany(ProductVariants::class, 'product_id');
    }

}
