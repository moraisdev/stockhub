<?php

namespace App\Http\Controllers\Supplier;

use Illuminate\Http\Request;

/* Requests */
use App\Http\Requests\Supplier\Login\AuthenticateRequest;
use App\Http\Requests\Supplier\Login\RegisterRequest;

use App\Models\Suppliers;

/* Services */
use App\Services\LoginService;
use App\Services\SafeToPayPlansService;

use Str;
use Auth;
use App\Models\Admins;

class LoginController extends Controller
{
    private $loginService;

    public function __construct(){
        $this->loginService = new LoginService('supplier');
    }

    public function index(Request $request){
        $redirect_url = url()->previous();
        $admins = Admins::find(2); 
        if(Str::contains($redirect_url, 'login') || !Str::contains($redirect_url, 'supplier')){
        
            
            $redirect_url = route('supplier.dashboard');
        }

        return view('supplier.login.index', compact('redirect_url' , 'admins' ));
    }

    public function authenticate(AuthenticateRequest $request){
        $authentication = $this->loginService->authenticate($request->email, $request->password, $request->keep_user_connected, $request);

        if($authentication->status == 'success'){
            //faz a atualização do plano do usuário antes dele entrar
            $supplier = Auth::guard('supplier')->user();
            
            //atualiza os dados do plano do usuário, pra não precisa ficar consultando o servidor do gateway de pagamento a cada requisição
            $safe2pay = new SafeToPayPlansService();

            $safe2pay->updateSupplierSubscription($supplier);

            return redirect($request->redirect_url)->with(['success_notification' => $authentication->message]);
        }else{
            return redirect()->back()->with(['error' => $authentication->message])->withInput($request->except('password'));
        }
    }

    public function register(Request $request){
        $email = $request->input('email');

        return view('supplier.login.register', compact('email'));
    }

    public function postRegister(RegisterRequest $request){
        $register = $this->loginService->register($request->name, $request->email, $request->password, $request->password_confirmation, $request->terms_agreed);

        if($register->status == 'success'){
            return redirect()->route('supplier.login')->with(['success' => $register->message]);
        }else{
            return redirect()->back()->with(['error' => $register->message])->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    public function forgotPassword(){
        return view('supplier.login.forgot_password');
    }

    public function postForgotPassword(Request $request){
        $email = $request->email;

        $recovery = $this->loginService->passwordRecovery($email);

        if($recovery->status == 'success'){
            return redirect()->route('supplier.login')->with(['success' => $recovery->message]);
        }else{
            return redirect()->back()->with(['error' => $recovery->message])->withInput($request->only(['email']));
        }
    }

    public function defineNewPassword($hash){
        $supplier = Suppliers::where('password_recovery_hash', $hash)->first();

        if(!$supplier){
            return redirect()->route('supplier.login.forgot_password')->with('error', 'Este link já expirou. Gere um novo link de recuperação de senha para definir uma nova senha.');
        }

        return view('supplier.login.define_new_password', compact('hash'));
    }

    public function postDefineNewPassword($hash, Request $request){
        $new_password = $this->loginService->defineNewPassword($hash, $request->password, $request->password_confirmation);

        if($new_password->status == 'success'){
            return redirect()->route('supplier.login')->with(['success' => $new_password->message]);
        }else{
            return redirect()->back()->with(['error' => $new_password->message]);
        }
    }

    public function logout(){
        $this->loginService->logout();

        return redirect()->route('supplier.login');
    }


}
