<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'customers';
    public $timestamps = true;

    public function address(){
    	return $this->hasOne(CustomerAddresses::class, 'customer_id');
    }

    public function orders(){
        return $this->hasMany(Orders::class, 'order_id');
    }
}
