<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariants extends Model
{
	use SoftDeletes;

    protected $guarded = [];
    protected $table = 'product_variants';
    public $timestamps = true;
    
    protected $fillable = ['product_id' , 'title','price','sku', 'weight_in_grams','height','width'];

  

    public function getFPriceAttribute(){
        return $this->currency.' '.$this->price;
    }

    public function options_values(){
    	return $this->hasMany(ProductVariantOptionsValues::class, 'product_variant_id')->with('option');
    }

    public function inventory(){
    	return $this->hasOne(ProductVariantInventories::class, 'product_variant_id');
    }

    public function product(){
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function stock(){
        return $this->hasOne(ProductVariantStock::class, 'product_variant_id');
    }
}
