<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopSuppliers extends Model
{
    use SoftDeletes;
	
    protected $guarded = [];
    protected $table = 'shop_suppliers';
    public $timestamps = true;
}
