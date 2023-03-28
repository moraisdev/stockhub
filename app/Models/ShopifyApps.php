<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopifyApps extends Model
{
    use SoftDeletes;
	
    protected $guarded = [];
    protected $table = 'shopify_apps';
    public $timestamps = true;
}
