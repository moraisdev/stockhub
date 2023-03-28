<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AliexpressProductVariants extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'aliexpress_product_variants';
    public $timestamps = true;
}
