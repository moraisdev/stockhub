<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopRadar extends Model
{
    protected $guarded = [];
    protected $table = 'shop_radar';

    public $timestamps = true;

    public function shop()
    {
        return $this->belongsTo(Shops::class, 'shop_id');
    }
}
