<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductVariants;

class ProductVariantStock extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'product_variant_stock';
    public $timestamps = true;

    public function productVariant()
    {
        return $this->belongsTo(ProductVariants::class, 'product_variant_id');
    }


}
