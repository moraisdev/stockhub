<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalSubscriptionShop extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'internal_subscription_shops';
    public $timestamps = true;
}
