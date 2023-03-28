<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopWoocommerceWebhooks extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'shop_woocommerce_webhooks';
    public $timestamps = true;
}
