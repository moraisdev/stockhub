<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderGroupPayments extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'order_group_payments';
    public $timestamps = true;

    public function group(){
        return $this->belongsTo(SupplierOrderGroup::class, 'group_id');
    }

    public function shop(){
        return $this->belongsTo(Shops::class, 'shop_id');
    }

    public function supplier(){
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }


}
