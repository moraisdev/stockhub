<?php

namespace App\Services;

use App\Models\Suppliers;
use App\Models\SupplierOrders;
use App\Models\SupplierOrderItems;

class SupplierOrdersService{

	public function __construct(Suppliers $supplier){
		$this->supplier = $supplier;
	}

	public function getDashboardData(){
        $week_profit = SupplierOrders::where('supplier_id', $this->supplier->id)->where('status', 'paid')->whereDate('created_at', '>=', date('Y-m-d', strtotime('last sunday')))->sum('amount');
        $total_profit = SupplierOrders::where('supplier_id', $this->supplier->id)->where('status', 'paid')->sum('amount');

        return compact('week_profit', 'total_profit');
    }

	public function getPendingOrders($limit = null){
		$orders = SupplierOrders::with('items')->with('order')
							 ->whereHas('shipping', function($q){
							 	$q->where('status', 'pending');
							 })
							 ->where('supplier_id', $this->supplier->id)
							 ->where('status', 'paid')
							 ->orderBy('id', 'desc');

		if($limit){
			$orders->limit($limit);
		}

		return $orders->get();
	}

	public function getPendingOrdersapi($limit = null){
		$orders = SupplierOrders::with('items')->with('order')
							 ->whereHas('shipping', function($q){
							 	$q->where('status', 'pending');
							 })
							 ->where('supplier_id', $this->supplier->id)
							 ->where('status', 'paid')
							 ->orderBy('id', 'desc');

		if($limit){
			$orders->limit($limit);
		}

		return $orders->get();
	}

	public function getSentOrders($limit = null){
		$orders = SupplierOrders::with('items')
							 ->whereHas('shipping', function($q){
							 	$q->where('status', 'sent');
							 })
							 ->where('supplier_id', $this->supplier->id)
							 ->where('status', 'paid')
							 ->orderBy('id', 'desc');

		if($limit){
			$orders->limit($limit);
		}

		return $orders->paginate(25);
	}

	public function getSentOrdersSearch($querySearch, $filter){
        //faz a query de acordo com o filtro passado
        if($filter == 'order_name'){
            // //adiciona os dados do lojista a busca
            // $orders = Orders::where('shop_id', $this->shop->id)
            //     ->where('status', 'pending')
            //     ->where('supplier_order_created', 0)
            //     ->whereHas('customer', function ($query) use ($querySearch) {
            //         $query->where('first_name', 'like', '%'.$querySearch.'%')//nome do cliente
            //                 ->orWhere('last_name', 'like', '%'.$querySearch.'%') //ultimo nome
            //                 ->orWhere('email', 'like', '%'.$querySearch.'%'); //email
            //     })
            //     ->select('orders.*')
            //     ->orderBy('id', 'desc')
			// 	->paginate(100);
			
			//procura pelo nome da ordem
			$orders = SupplierOrders::with('items')
				->whereHas('shipping', function($q){
					$q->where('status', 'sent');
				})
				->whereHas('order', function ($query) use ($querySearch) {
                    $query->where('name', 'like', '%'.$querySearch.'%'); //nome da ordem
                })
				->where('supplier_id', $this->supplier->id)
				->where('status', 'paid')
				->orderBy('id', 'desc')
				->paginate(100);
                
		}else if($filter == 'shop'){
			// //procura pelo nome da loja
			$orders = SupplierOrders::with('items')
				->whereHas('shipping', function($q){
					$q->where('status', 'sent');
				})
				// ->whereHas('order', function ($query) use ($querySearch) {
                //     $query->where('name', 'like', '%'.$querySearch.'%'); //nome da ordem
                // })
				// ->where(function($q) use ($querySearch){
				// 	$q->select('name')
				// 		->from('shops')
				// 		->whereColumn('shops.name','like', '%'.$querySearch.'%')
				// 		->limit(1);
				// }, 'shop')
				->join('orders', 'orders.id', '=', 'supplier_orders.order_id')
				->join('shops', 'shops.id', '=', 'orders.shop_id')
				->where('shops.name','like', '%'.$querySearch.'%')
				->where('supplier_orders.supplier_id', $this->supplier->id)
				->where('supplier_orders.status', 'paid')
				->select('supplier_orders.*')
				->orderBy('id', 'desc')
				->paginate(100);

		}else if($filter == 'display_id'){
			//caso seja esse display_id, retira o F se a pessoa digitou pq n precisa
			if($querySearch[0] == 'F' || $querySearch[0] == 'f'){
				$querySearch = substr($querySearch, 1);
			}

			$orders = SupplierOrders::with('items')
				->whereHas('shipping', function($q){
					$q->where('status', 'sent');
				})
				->where('supplier_id', $this->supplier->id)
				->where('status', 'paid')
				->where($filter, 'like', '%'.$querySearch.'%') //filtro
				->orderBy('id', 'desc')
				->paginate(100);
		}else if($filter == 'tracking_number'){
			$orders = SupplierOrders::with('items')
				->whereHas('shipping', function($q) use ($querySearch){
					$q->where('status', 'sent')
						->where('tracking_number', 'like', '%'.$querySearch.'%');
				})
				->where('supplier_id', $this->supplier->id)
				->where('status', 'paid')
				->orderBy('id', 'desc')
				->paginate(100);
		}else if($filter == 'created_at'){			
			$orders = SupplierOrders::with('items')
					->whereHas('shipping', function($q){
						$q->where('status', 'sent');
					})
					->where('supplier_id', $this->supplier->id)
					->where('status', 'paid')
					->where($filter, 'like', '%'.implode("-",array_reverse(explode("/", $querySearch))).'%') 
					->orderBy('id', 'desc')
					->paginate(100);
        }else{
			$orders = SupplierOrders::with('items')
				->whereHas('shipping', function($q){
					$q->where('status', 'sent');
				})
				->where('supplier_id', $this->supplier->id)
				->where('status', 'paid')
				->where($filter, 'like', '%'.$querySearch.'%') //filtro
				->orderBy('id', 'desc')
				->paginate(100);
        }

        return $orders;
    }

	public function getCompletedOrders($limit = null){
		$orders = SupplierOrders::with('items')
							 ->whereHas('shipping', function($q){
							 	$q->where('status', 'completed');
							 })
							 ->where('supplier_id', $this->supplier->id)
							 ->where('status', 'paid')
							 ->orderBy('id', 'desc');

		if($limit){
			$orders->limit($limit);
		}

		return $orders->get();
	}

	public function getReturnedOrders($limit = null){
		$orders = SupplierOrders::where('supplier_id', $this->supplier->id)
							 ->where('status', 'returned')
							 ->orderBy('id', 'desc');

		if($limit){
			$orders->limit($limit);
		}

		return $orders->get();
	}

	public function recalcSupplierOrderAmount($supplier_order){
	    $products_total = 0;
	    foreach ($supplier_order->items as $item){
	        $products_total += $item->amount;
        }

	    $supplier_order->amount = $products_total;
	    $supplier_order->total_amount = $supplier_order->shipping->amount + $products_total;
	    $supplier_order->save();

    }
}
