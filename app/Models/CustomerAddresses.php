<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddresses extends Model
{
    use SoftDeletes;
	
    protected $guarded = [];
    protected $table = 'customer_addresses';
    public $timestamps = true;
}
