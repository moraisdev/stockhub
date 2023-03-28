<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponInternalSubscriptionShop extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'coupon_internal_subscription_shops';
    public $timestamps = true;
}
