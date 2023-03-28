<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierCanceledPlans extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'supplier_canceled_plans';
    public $timestamps = true;
}
