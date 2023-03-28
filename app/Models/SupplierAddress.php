<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierAddress extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'supplier_address';
    public $timestamps = true;
}
