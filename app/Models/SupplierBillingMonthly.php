<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierBillingMonthly extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'supplier_billing_monthlies';
    public $timestamps = true;
}
