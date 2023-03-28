<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AliexpressProducts extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'aliexpress_products';
    public $timestamps = true;

    public function variants(){
    	return $this->hasMany(AliexpressProductVariants::class, 'product_id');
    }

    public function options(){
    	return $this->hasMany(AliexpressProductOptions::class, 'product_id');
    }
}
