<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FunctionsController extends Controller
{
    // 451 - loja pradus
    // 173 - inovante
    // 7 - teste jéssica
    // 349 - teste eric
    // 482 - Kyngler
    // 148 - Kabannishop
    // 623 - Grazi Loja Virtual
    // 253 - Diogenes Soares
    // 594 - Schazmann
    // 593 - KSIMPORT
    // 267 - Hebert Nery

    // planos allan (remover essa galera dps)
    // 607 - Inovarem
    // 798 - Eduardo (Serpa Embalagens)
    // 799 - Rafael de Araujo Bazilio
    // 802 - Insight Casa
    // 822 - Uillian
    // 835 - Zoom Criativo
    // 878 - Comando Bebê (Pedro)
    // 356 - Juno Store (Allan)
    // 968 -  Tryail (Pedro)
    // 1199 - Filippe

    //257 - MARVIN WALISON MACHADO
    //1282 - Linda lar (Eric)

    //após o lançamento
    //1284 - Shopping Girassol

    //1412 - MixCommerce
    //1413 - Uzzebox

    //1430 - Liquidafast

    static $freeShops = [10, 451, 173, 7, 349, 482, 148, 623, 253, 594, 593, 267, 607, 798, 799, 802, 822, 835, 878, 356, 968, 1199, 257, 1282, 1284, 1412, 1413, 1430];

    public static function freeShop($shop){
        return in_array($shop->id, self::getFreeShops());
    }

    public static function getFreeShops(){
        return self::$freeShops;
    }
}
