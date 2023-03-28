<?php

namespace App\Http\Controllers\Supplier;

use Illuminate\Http\Request;

class ChargesController extends Controller
{
    public function index(){
    	$charges = [];

    	return view('supplier.charges.index', compact('charges'));
    }
}
