<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierContractedPlans extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'supplier_contracted_plans';
    public $timestamps = true;
}
