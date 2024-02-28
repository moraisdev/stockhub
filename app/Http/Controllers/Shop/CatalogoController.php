<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

class CatalogoController extends Controller
{
    public function catalog()
    {
        return view('shop.catalog.index');
    }
}

