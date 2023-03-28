<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Models\Tutorial;

class TutorialsController extends Controller
{
    public function index(){
        $tutorial = Tutorial::all();
       
        return view('shop.tutorials.index', compact('tutorial'));
    }
}
