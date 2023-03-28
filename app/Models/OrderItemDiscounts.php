<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemDiscounts extends Model
{
    protected $guarded = [];
    protected $table = 'order_item_discounts';
    public $timestamps = true;

    public function discount(){
    	return $this->belongsTo(ProductDiscounts::class, 'product_discount_id');
    }
}
