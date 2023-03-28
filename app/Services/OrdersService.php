<?php

namespace App\Services;

use App\Exceptions\CustomException;

use App\Models\Orders;
use App\Models\Suppliers;
use Auth;

class OrdersService{

    public $user, $order;

    public function __construct($order=null){
        $this->user = Auth::user();
        $this->order = $order;
    }

    public static function getAll(){
        return Orders::all();
    }

    public static function getPaidOrders(){
    	return Orders::all();
    }
}

