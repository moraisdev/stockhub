<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierOrdersDiscounts extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'supplier_orders_discounts';
    public $timestamps = true;

    public function discount(){
        return $this->belongsTo(Discounts::class, 'discount_id');
    }

    public function group(){
        return $this->belongsTo(SupplierOrderGroup::class, 'group_id');
    }
}
