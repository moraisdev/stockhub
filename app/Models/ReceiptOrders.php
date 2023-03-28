<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptOrders extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'receipt_orders';
    public $timestamps = true;
}
