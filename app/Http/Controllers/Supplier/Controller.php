<?php

namespace App\Http\Controllers\Supplier;

use App\Models\ReturnMessages;
use App\Models\Returns;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use View;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(){
    	$this->middleware(function($request, $next){
    	    $supplier = Auth::guard('supplier')->user();

    	    $supplier_id = $supplier->id;

            $returns = Returns::whereHas('supplier_order', function($q) use ($supplier_id){
                $q->where('supplier_id', $supplier_id);
            })->where('status', '!=', 'resolved')->get();

            if($returns){
                $return_ids = $returns->pluck('id')->toArray();

                $pending_return_messages_count = ReturnMessages::whereIn('return_id', $return_ids)->where('read', 0)->whereNull('supplier_id')->count();
            }else{
                $pending_return_messages_count = 0;
            }

            View::share('authenticated_user', $supplier);
            View::share('pending_return_messages_count', $pending_return_messages_count);

    		return $next($request);
    	});
    }
}
