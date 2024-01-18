<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

/* Requests */
use App\Http\Requests\Supplier\Login\AuthenticateRequest;
use App\Http\Requests\Supplier\Login\RegisterRequest;

use App\Models\Admins;

/* Services */
use App\Services\LoginService;

use Str;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(){
        $this->loginService = new LoginService('admin');
    }

    public function index(Request $request){
        return view('admin.login.index');
    }

    public function authenticate(AuthenticateRequest $request){
        $authentication = $this->loginService->authenticate($request->email, $request->password, $request->keep_user_connected, $request);
        
        if($authentication->status == 'success'){
            return redirect()->route('admin.dashboard')->with(['success_notification' => $authentication->message]);
        }else{
            return redirect()->back()->with(['error' => $authentication->message])->withInput($request->except('password'));
        }
    }

    public function logout(){
        $this->loginService->logout();

        return redirect()->route('admin.login');
    }
}