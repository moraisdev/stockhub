<?php

namespace App\Services;

use App\Exceptions\CustomException;

use Illuminate\Http\Request;

/* Models */
use App\Models\ShopSuppliers;
use App\Models\SupplierOrders;

class PartnersService{

    public static function getShopOrdersBySupplier($shop, $supplier){
        return SupplierOrders::select('supplier_orders.*')
                             ->rightJoin('orders', 'supplier_orders.order_id', '=', 'orders.id')
                             ->where('orders.shop_id', $shop->id)
                             ->where('supplier_orders.supplier_id', $supplier->id)
                             ->get();
    }

    public static function link(int $shop_id, int $supplier_id){
        $link = ShopSuppliers::firstOrNew(['shop_id' => $shop_id, 'supplier_id' => $supplier_id]);

        if($link->id == null){
        	$link->date = date('Y-m-d');
        }

        if(!$link->save()){
            return false;
        }

        return true;
    }
}
