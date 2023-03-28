<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnMessages extends Model
{
    use SoftDeletes;

    protected $table = 'return_messages';
    protected $guarded = [];

    public function return(){
        return $this->belongsTo(Returns::class, 'return_id');
    }

    public function supplier(){
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }

    public function shop(){
        return $this->belongsTo(Shops::class, 'shop_id');
    }

}
