<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierOrderGroup extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'supplier_order_group';
    public $timestamps = true;

    public function orders(){
        return $this->hasMany(SupplierOrders::class, 'group_id')->with('order');
    }

    public function shop(){
        return $this->belongsTo(Shops::class, 'shop_id');
    }

    public function discounts(){
        return $this->hasMany(SupplierOrdersDiscounts::class, 'group_id');
    }

    public function discounts_returneds(){
        return $this->hasMany(DiscountAppliedOrderRefunded::class, 'supplier_order_group_id', 'id');
    }
}
