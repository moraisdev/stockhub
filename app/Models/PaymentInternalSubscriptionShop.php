<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentInternalSubscriptionShop extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'payment_internal_subscription_shops';
    public $timestamps = true;
}
