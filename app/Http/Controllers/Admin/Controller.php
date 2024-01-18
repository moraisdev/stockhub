<?php

namespace App\Http\Controllers\Admin;

use App\Models\Suppliers;
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
            View::share('authenticated_user', Auth::guard('admin')->user());

    		return $next($request);
    	});
    }
}
