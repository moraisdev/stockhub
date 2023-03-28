<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FreteMelhorEnvio extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'frete_melhor_envios';
    public $timestamps = true;

    public function supplier_order(){
        return $this->belongsTo(SupplierOrders::class, 'supplier_order_id');
    }
}
