<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderShippings extends Model
{
    use SoftDeletes;

    public $table = 'order_shippings';
    public $timestamps = true;
    protected $guarded = [];
}
