<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TokenCardShop extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'token_card_shops';
    public $timestamps = true;

    public function used_coupon(){
        return $this->hasOne(UsedCouponInternalSubscriptionShop::class, 'token_card_id');
    }
}