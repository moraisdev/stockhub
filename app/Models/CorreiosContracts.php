<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorreiosContracts extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'correios_contracts';

    public function supplier(){
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }
}
