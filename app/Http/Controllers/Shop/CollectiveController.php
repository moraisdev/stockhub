<?php
namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Models\ShopAddress;

use Auth;

class CollectiveController extends Controller
{
    public function index(){

        return view('shop.collective.index');
    }
}
