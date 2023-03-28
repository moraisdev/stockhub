<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountAppliedOrderRefunded extends Model
{
    protected $guarded = [];

    public function order_returned(){
        return $this->belongsTo(OrderReturned::class, 'order_returned_id');
    }
}
