<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopShopifyWebhooks extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'shop_shopify_webhooks';
    public $timestamps = true;
}
