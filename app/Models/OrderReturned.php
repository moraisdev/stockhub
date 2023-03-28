<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderReturned extends Model
{
    use SoftDeletes;
	
    protected $guarded = [];
    protected $table = 'order_returneds';
    public $timestamps = true;

    public function order(){
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function supplier_order(){
    	return $this->belongsTo(SupplierOrders::class, 'supplier_order_id');
    }
}
