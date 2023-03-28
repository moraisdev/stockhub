<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierGateways extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'supplier_gateways';
    public $timestamps = true;

    public function supplier(){
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }

}
