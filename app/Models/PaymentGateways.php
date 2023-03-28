<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGateways extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'payment_gateways';
    public $timestamps = true;
}
