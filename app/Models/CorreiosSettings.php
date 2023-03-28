<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorreiosSettings extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'correios_settings';

    public function supplier(){
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }
}
