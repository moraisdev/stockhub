<?php

namespace App\Imports;

use App\Models\OrderItems;
use App\Models\ShopProducts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class OrdersItemsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $shop = Auth::guard('shop')->user();
        // $shop_id = $shop->id;
        $order_id = DB::Select('select id from orders where external_id = '.$row[0].' limit 1');
        $product_variant = DB::Select('select id from product_variants where sku = "'.$row[1].'" limit 1');
        $product_title = DB::Select('select title from product_variants where sku = "'.$row[1].'" limit 1');
        
        // dd($order_id[0]->id, $product_variant[0]->id, $product_title[0]->title);
        return new OrderItems([
            'order_id' => $order_id[0]->id,
            'product_variant_id' => $product_variant[0]->id,
            'external_service' => 'planilha',
            'external_product_id' => $row[2],
            'external_variant_id' => $row[3],
            'sku' => $row[1],
            'title' => $product_title[0]->title,
            'quantity' => $row[4],
            'amount' => $row[5],
            'external_price' => $row[6],
            'charge' => 1,            
            ]);
    }

}
