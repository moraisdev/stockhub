<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WoocommerceApps extends Model
{
    use SoftDeletes;
	
    protected $guarded = [];
    protected $table = 'woocommerce_apps';
    public $timestamps = true;
}
