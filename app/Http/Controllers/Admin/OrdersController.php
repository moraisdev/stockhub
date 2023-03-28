<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Models\Orders;
use App\Models\SupplierOrders;

class OrdersController extends Controller
{
    public function shops(){
        $orders = Orders::has('shop')->where('status', 'pending')->orderBy('id', 'desc')->paginate(100);

        return view('admin.orders.shops', compact('orders'));
    }

    public function showShopOrder(Orders $order){
        return view('admin.orders.show_shop_order', compact('order'));
    }

    public function suppliers(){
        $orders = SupplierOrders::with('items')
            ->whereHas('shipping', function($q){
                $q->where('status', 'pending');
            })
            ->where('status', 'paid')
            ->orderBy('id', 'desc')
            ->paginate(100);

        return view('admin.orders.suppliers', compact('orders'));
    }

    public function showSupplierOrder(SupplierOrders $supplier_order){
        return view('admin.orders.show_supplier_order', compact('supplier_order'));
    }

    public function printTag(SupplierOrders $supplier_order){
        $supplier = $supplier_order->supplier;

        if(!$supplier->address){
            return redirect()->back()->with('error', 'O fornecedor ainda não tem um endereço cadastrado.');
        }

        return view('admin.orders.tag', compact('supplier_order'));
    }

    public function printContentDeclaration(SupplierOrders $supplier_order){
        $supplier = $supplier_order->supplier;

        if(!$supplier->address){
            return redirect()->back()->with('error', 'O fornecedor ainda não tem um endereço cadastrado.');
        }

        if(!$supplier_order->order || !$supplier_order->order->customer || !$supplier_order->order->customer->address){
            return redirect()->back()->with('error', 'Não há nenhum cliente atribuído à este pedido e não é possível gerar a declaração de conteúdo.');
        }

        return view('admin.orders.content_declaration', compact('supplier', 'supplier_order'));
    }
}
