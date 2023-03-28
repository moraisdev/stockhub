<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCanceledPlans extends Model
{
    protected $table = 'shop_canceled_plans';
    public $timestamps = true;

    protected $fillable = ['shop_id'];

}
