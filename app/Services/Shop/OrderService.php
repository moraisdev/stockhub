<?php

namespace App\Services\Shop;

use App\Models\Orders;
use App\Models\OrderItems;
use App\Models\ProductVariants;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;
use App\Services\CurrencyService;

use App\Models\SupplierOrderGroup;
use App\Models\SupplierOrderItems;
use App\Models\SupplierOrders;
use App\Models\SupplierOrderShippings;
use App\Models\Suppliers;

class OrderService  {
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_hash' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        DB::beginTransaction();

        try {
            $shop = Auth::guard('shop')->user();
        
            $product = Products::where('hash', $request->product_hash)->first();
        
            if (!$product) {
                throw new CustomException('Product not found', 404);
            }
        
            // Supondo que você queira buscar todas as variantes desse produto
            $variants = ProductVariants::where('product_id', $product->id)->get();
        
            if ($variants->isEmpty()) {
                throw new CustomException('No variants found for this product', 404);
            }
        
            // Exemplo de processamento para todas as variantes
            foreach ($variants as $variant) {
                // Aqui, você pode calcular o total para cada variante, criar ordens, etc.
                $totalAmount = $variant->price * $request->quantity;
                
                // Supondo que você queira criar uma ordem para cada variante
                $order = Orders::create([
                    'shop_id' => $shop->id,
                    'customer_id' => $shop->id,
                    'external_service' => null,
                    'external_id' => Str::random(10),
                    'name' => $shop->name,
                    'email' => $shop->email,
                    'items_amount' => $totalAmount,
                    'shipping_amount' => 0,
                    'amount' => $totalAmount,
                    'external_price' => $totalAmount,
                    'status' => 'pending',
                    'external_created_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        
                // Criar o item do pedido
                OrderItems::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id,
                    'external_service' => null,
                    'sku' => $variant->sku,
                    'title' => $variant->title,
                    'quantity' => $request->quantity,
                    'amount' => $totalAmount,
                    'external_price' => $variant->price,
                    'charge' => 1,
                ]);
            }
        
            DB::commit();
            return response()->json(['message' => 'Orders created successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create order', 'exception' => $e->getMessage()], 500);
        }
        
    }


    public static function generate_supplier_orders($orders){
        $suppliers = [];
        $total_to_pay = 0;

        $dolar_price = CurrencyService::getDollarPrice();

        $group = new SupplierOrderGroup();
        $group->shop_id = Auth::guard('shop')->id();
        $group->description = date('d/m/Y H:i:s');
        $group->status = 'pending';
        $group->save();

        foreach($orders as $order){
            if($order->supplier_order_created == 0) {
                foreach ($order->items as $item) {
                    if($item->variant && $item->variant->product && $item->variant->product->supplier){
                        $suppliers[$item->variant->product->supplier->id][$item->order_id][] = $item;
                    }else{ //caso a variante tenha sido excluida
                        $variant = ProductVariants::withTrashed()->find($item->product_variant_id);

                        if($variant){
                            $product = Products::withTrashed()->find($variant->product_id);

                            if($product){
                                $supplier = Suppliers::withTrashed()->find($product->supplier_id);
                                if($supplier){
                                    $suppliers[$supplier->id][$item->order_id][] = $item;
                                }
                            }
                        }
                    }
                }

                $order->supplier_order_created = 1;
                $order->save();
            }
        }

        foreach($suppliers as $supplier_id => $supplier_order){
            foreach($supplier_order as $order_id => $items){
                $new_order = new SupplierOrders();
                $new_order->order_id = $order_id;
                $new_order->supplier_id = $supplier_id;
                $new_order->status = 'pending';
                $new_order->group_id = $group->id;

                $new_order->save();

                $order = Orders::with('shippings')->find($order_id);

                $total_shipping = $order->shippings->where('supplier_id', $supplier_id)->sum('amount');

                $shipping = new SupplierOrderShippings();
                $shipping->supplier_id = $supplier_id;
                $shipping->supplier_order_id = $new_order->id;
                $shipping->amount = $total_shipping;
                $shipping->status = 'pending';
                $shipping->external_service = $order->external_service;
                $shipping->tracking_url = $order->tracking_url;
                $shipping->tracking_number =  $order->tracking_number;
                $shipping->company = $order->tracking_servico;
                $shipping->save();

                $total_amount = 0;

                foreach($items as $item){
                    //$discount = Discounts::where('variant_id', $item->product_variant_id)->first();
                    $discount = false;

                    $new_item = new SupplierOrderItems();
                    $new_item->supplier_order_id = $new_order->id;
                    $new_item->product_variant_id = $item->product_variant_id;

                    //antes de atribuir o preço do item, verificar se a variant ta em dólar
                    //precisa realizar a cotação e multiplicar
                    if($item->variant && $item->variant->product){
                        if($item->variant->product->currency == 'US$'){

                            if(isset($dolar_price['price'])){
                                $amount = $item->amount * $dolar_price['price'];
                            }else{
                                $amount = $item->amount * 1000;
                            }
                        }else{
                            $amount = $item->amount;
                        }

                        if($discount){
                            $new_item->amount = $amount - ($amount * ($discount->percentage/100));
                        }else{
                            $new_item->amount = $amount;
                        }

                        if($item->variant->product->supplier->id == 56){
                            $new_item->amount = $new_item->amount * 1.05;
                        }

                        $new_item->quantity = $item->quantity;

                        $new_item->save();

                        $total_amount += $new_item->amount;
                    }else{ //caso a variante tenha sido excluida
                        $variant = ProductVariants::withTrashed()->find($item->product_variant_id);

                        if($variant){
                            $product = Products::withTrashed()->find($variant->product_id);

                            if($product){
                                if($product->currency == 'US$'){

                                    if(isset($dolar_price['price'])){
                                        $amount = $item->amount * $dolar_price['price'];
                                    }else{
                                        $amount = $item->amount * 1000;
                                    }
                                }else{
                                    $amount = $item->amount;
                                }

                                if($discount){
                                    $new_item->amount = $amount - ($amount * ($discount->percentage/100));
                                }else{
                                    $new_item->amount = $amount;
                                }

                                if($product->supplier->id == 56){
                                    $new_item->amount = $new_item->amount * 1.05;
                                }

                                $new_item->quantity = $item->quantity;

                                $new_item->save();

                                $total_amount += $new_item->amount;
                            }
                        }
                    }

                }

                $new_order->amount = $total_amount;
                $new_order->total_amount = $total_amount + $total_shipping;

                $new_order->save();
            }
        }

        $total_to_pay = SupplierOrders::whereIn('order_id', $orders->pluck('id'))->sum('total_amount');

        return ['total_in_supplier_orders' => $total_to_pay, 'order_ids' => $orders->pluck('id')];
    }
    public static function translateStatus($status){
	    switch ($status){
            case 'pending':
                return 'Pendente';
                break;
            case 'paid':
                return 'Pago';
                break;
            default:
                return 'Desconhecido';
                break;
        }

    }

    public static function generate_supplier_orders_individual_order($order){
        $suppliers = [];
        $total_to_pay = 0;

        $dolar_price = CurrencyService::getDollarPrice();

        $group = new SupplierOrderGroup();
        $group->shop_id = Auth::guard('shop')->id();
        $group->description = date('d/m/Y H:i:s');
        $group->status = 'pending';
        $group->save();

        if($order->supplier_order_created == 0) {
            foreach ($order->items as $item) {
                if($item->variant && $item->variant->product && $item->variant->product->supplier){
                    $suppliers[$item->variant->product->supplier->id][$item->order_id][] = $item;
                }else{ //caso a variante tenha sido excluida
                    $variant = ProductVariants::withTrashed()->find($item->product_variant_id);

                    if($variant){
                        $product = Products::withTrashed()->find($variant->product_id);

                        if($product){
                            $supplier = Suppliers::withTrashed()->find($product->supplier_id);
                            if($supplier){
                                $suppliers[$supplier->id][$item->order_id][] = $item;
                            }
                        }
                    }
                }
            }

            $order->supplier_order_created = 1;
            $order->save();
        }

        foreach($suppliers as $supplier_id => $supplier_order){
            foreach($supplier_order as $order_id => $items){
                $new_order = new SupplierOrders();
                $new_order->order_id = $order_id;
                $new_order->supplier_id = $supplier_id;
                $new_order->status = 'pending';
                $new_order->group_id = $group->id;

                $new_order->save();

                $orderAux = Orders::with('shippings')->find($order_id);
                //dd($orderAux->shippings);
                $total_shipping = $orderAux->shippings->where('supplier_id', $supplier_id)->sum('amount');

                //dd($total_shipping);

                $shipping = new SupplierOrderShippings();
                $shipping->supplier_id = $supplier_id;
                $shipping->supplier_order_id = $new_order->id;
                $shipping->amount = $total_shipping;
                $shipping->status = 'pending';
                $shipping->external_service = $orderAux->external_service;
                $shipping->save();

                $total_amount = 0;

                foreach($items as $item){
                    //$discount = Discounts::where('variant_id', $item->product_variant_id)->first();
                    //$discount = false;

                    $new_item = new SupplierOrderItems();
                    $new_item->supplier_order_id = $new_order->id;
                    $new_item->product_variant_id = $item->product_variant_id;

                    //antes de atribuir o preço do item, verificar se a variant ta em dólar
                    //precisa realizar a cotação e multiplicar
                    if($item->variant && $item->variant->product){

                        $new_item->amount = 0;
                        $new_item->quantity = $item->quantity;

                        $new_item->save();

                        $total_amount += $new_item->amount;

                    }else{ //caso a variante tenha sido excluida
                        $variant = ProductVariants::withTrashed()->find($item->product_variant_id);

                        if($variant){
                            $product = Products::withTrashed()->find($variant->product_id);

                            if($product){

                                $new_item->amount = 0;

                                $new_item->quantity = $item->quantity;

                                $new_item->save();

                                $total_amount += $new_item->amount;
                            }
                        }
                    }

                }

                $new_order->amount = $total_amount;
                $new_order->total_amount = $total_amount + $total_shipping;

                $new_order->save();
            }
        }

        $total_to_pay = SupplierOrders::where('order_id', $order->id)->sum('total_amount');

        return ['total_in_supplier_orders' => $total_to_pay, 'order_id' => $order->id];
    }
}
