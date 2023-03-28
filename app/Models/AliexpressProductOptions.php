<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AliexpressProductOptions extends Model
{
    use SoftDeletes;
	
    protected $guarded = [];
    protected $table = 'aliexpress_product_options';
    public $timestamps = true;
}
