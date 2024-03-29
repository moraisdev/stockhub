<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierBank extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'supplier_bank';
    public $timestamps = true;
}
