<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopContractedPlans extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'shop_contracted_plans';
    public $timestamps = true;
}
