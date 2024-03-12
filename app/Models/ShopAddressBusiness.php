<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopAddressBusiness extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'shop_address_business';
    public $timestamps = true;
}
