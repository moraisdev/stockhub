<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopAddress extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'shop_address';
    public $timestamps = true;

    public function shop()
    {
        return $this->belongsTo(Shops::class, 'shop_id');
    }
}
