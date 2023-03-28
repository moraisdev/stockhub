<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItems extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'order_items';
    public $timestamps = true;

    public function variant(){
    	return $this->belongsTo(ProductVariants::class, 'product_variant_id');
    }

    public function order(){
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function discount_applied(){
        return $this->hasOne(OrderItemDiscounts::class, 'order_item_id');
    }
}
