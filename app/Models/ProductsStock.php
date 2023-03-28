<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductsStock extends Model
{
    use SoftDeletes;
    public $table = 'products_stock';
    protected $guarded = [];
    public $timestamps = true;
    public $service;

}
