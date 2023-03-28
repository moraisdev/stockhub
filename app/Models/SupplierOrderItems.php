<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierOrderItems extends Model
{
    use SoftDeletes;
	
    protected $guarded = [];
    protected $table = 'supplier_order_items';
    public $timestamps = true;

    public function variant(){
    	return $this->belongsTo(ProductVariants::class, 'product_variant_id');
    }

    public function order(){
    	return $this->belongsTo(SupplierOrders::class, 'supplier_order_id');
    }
}
