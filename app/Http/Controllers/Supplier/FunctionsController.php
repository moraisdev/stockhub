<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FunctionsController extends Controller
{
    public static function supplierOrderAmount($order){
        //56 - s2m2
        //43 - ksimports
        if($order->supplier->id == 56 || $order->supplier->id == 43){
            return number_format($order->total_amount, 2, ',', '.');
        }
        return number_format($order->amount, 2, ',', '.');; //retorna o valor sem o frete
    }
}
