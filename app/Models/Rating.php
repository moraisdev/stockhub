<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{

    protected $fillable = [
        'product_id',
        'shop_id',
        'rating',
        'comment'
    ];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shops::class);
    }
}
