<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImages extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'product_images';
    public $timestamps = true;

    public function product(){
    	return $this->belongsTo(Products::class, 'product_id');
    }
}
