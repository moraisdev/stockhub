<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SupplierOrdersService;
use App\Models\Suppliers;
use App\Models\SupplierOrders;
use App\Models\SupplierOrderItems;
use Illuminate\Support\Facades\DB;



class OrdersController extends Controller
{
    public function index(){
		
         $supplier = Suppliers::find(1);
		 $shipping_status = 'pending';
        
         $orders_supplier = SupplierOrders::select('amount','total_amount', 'status', 'order_id' )->get();
         
        $service = new SupplierOrdersService($supplier);
		$orders = $service->getPendingOrders();
		
		//return $orders;

        $resultados = DB::table('supplier_orders')
                ->join('supplier_order_items', 'supplier_orders.id', '=', 'supplier_order_items.supplier_order_id')
                ->join('product_variants', 'supplier_order_items.product_variant_id', '=', 'product_variants.product_id')
                ->join('orders', 'supplier_orders.order_id', '=', 'orders.id')
                ->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->select('supplier_orders.id', 'supplier_orders.amount', 'supplier_order_items.product_variant_id', 'product_variants.id', 'product_variants.sku', 'product_variants.title', 'orders.external_service', 'orders.status','orders.items_amount', 'customers.email',  'customer_addresses.address1' ,  'customer_addresses.number',  'customer_addresses.city',  'customer_addresses.zipcode',  'customer_addresses.phone')
                ->get();
		
	
    return response()->json($resultados);
		
		
  
		
		
		

    }   
	
	public function api(){
		
         $supplier = Suppliers::find(1);
		 $shipping_status = 'pending';
        
        $service = new SupplierOrdersService($supplier);
		$orders = $service->getPendingOrdersapi();
		
		return $orders;
		
		
		
		

    } 
	
	
}
