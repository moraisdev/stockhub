<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantOptionsValues extends Model
{
    protected $guarded = [];
    protected $table = 'product_variant_options_values';
    public $timestamps = true;

    public function option(){
    	return $this->belongsTo(ProductOptions::class, 'product_option_id');
    }
}
