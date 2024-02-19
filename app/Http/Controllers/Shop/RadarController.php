<?php
namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Models\ShopAddress;

use Auth;

class RadarController extends Controller
{
    public function index(){

        return view('shop.radar.index');
    }


    public function buy(){

        return view('shop.radar.buy');
    }
    public function activate(){

        return view('shop.radar.activate');
    }
}
