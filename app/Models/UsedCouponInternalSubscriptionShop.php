<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsedCouponInternalSubscriptionShop extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'used_coupon_internal_subscription_shops';
    public $timestamps = true;
}
