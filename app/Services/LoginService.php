<?php

namespace App\Services;

use App\Exceptions\CustomException;

/* Models */
use App\Models\Suppliers;
use App\Models\Shops;
use App\Models\Admins;

use App\Mail\PasswordRecoveryMail;
use App\Mail\Welcome;
use App\Mail\ApprovedRegistration;
use App\Models\Store_invoice;
use App\Models\Supplier_invoice;
use App\Models\ShopContractedPlans;
use App\Models\SupplierContractedPlans;



/* Libraries */
use Hash;
use Auth;
use Str;
use Mail;

class LoginService{
    protected $guard, $model;

    public function __construct($guard){
        if($guard == 'shop'){
            $this->guard = 'shop';
            $this->model = Shops::class;
        }elseif($guard == 'admin'){
            $this->guard = 'admin';
            $this->model = Admins::class;
        }else{
            $this->guard = 'supplier';
            $this->model = Suppliers::class;
        }
    }

    public function authenticate($email, $password, $keep_user_connected = 0, $request = null){
        $user = $this->model::where('email', $email)->first();

        if($user){
            if(Hash::check($password, $user->password)){
                if(($this->guard == 'supplier' || $this->guard == 'shop') && $user->login_status == 'unauthorized'){
                    return (object)(['status' => 'error', 'message' => 'Você não possui autorização para efetuar login no '.config('app.name').'. Em caso de dúvidas, entre em contato com nossa equipe de suporte.']);
                }

                Auth::guard($this->guard)->login($user, $keep_user_connected);

                return (object)(['status' => 'success', 'message' => 'Login efetuado com sucesso.']);
            }else{
                return (object)(['status' => 'error', 'message' => 'Senha inválida. Tente novamente.']);
            }
        }else{
            return (object)(['status' => 'error', 'message' => 'Não há nenhum usuário cadastrado com este e-mail.']);
        }
    }

    public function register($name, $email, $password, $password_confirmation, $terms_agreed, $phone = NULL, $document = NULL){
        if(($this->guard == 'supplier' || $this->guard == 'shop') && $terms_agreed != 'on'){
            return (object)['status' => 'error', 'message' => 'Você precisa confirmar com os termos e condições de uso do '.config('app.name').' para concluir seu cadastro'];
        }

        $already_registered = $this->model::where('email', $email)->first();

        if($already_registered){
            return (object)(['status' => 'error', 'message' => 'Este e-mail já foi cadastrado.']);
        }else{
            if(!$phone && $this->guard == 'shop'){
                return (object)(['status' => 'error', 'message' => 'Telefone inválido.']);
            }

            if(!$document && $this->guard == 'shop'){
                return (object)(['status' => 'error', 'message' => 'CPF ou CNPJ inválido.']);
            }

            if($password == $password_confirmation){
                $user = $this->createUser($name, $email, $password, $phone, $document);
                
                if($this->guard == 'shop'){
                    Mail::to($user->email)->send(new Welcome($user));
                    Mail::to($user->email)->send(new ApprovedRegistration($user));
                
                    $admins = Admins::find(2);
                    if($admins->free_shop != 0){
                    $store_invoice =  new Store_invoice();
                    $store_invoice->shop_id = $user->id; 
                    $store_invoice->plan = 'FREE';
                    $store_invoice->sub_total = '0.00';
                    $store_invoice->total = '0.00';
                    $store_invoice->status = 'active';
                    $store_invoice->payment = 'paid';
                    $store_invoice->due_date = date('Y-m-d', strtotime(+$admins->free_shop.'days', strtotime($user->created_at)));
                    $store_invoice->save();

                    $shop_contrac_plan = new  ShopContractedPlans();
                    $shop_contrac_plan->shop_id = $user->id;
                    $shop_contrac_plan->name_plan ='FREE';
                    $shop_contrac_plan->subscription_status = 'active';
                    $shop_contrac_plan->due_date  = date('Y-m-d', strtotime(+$admins->free_shop.'days', strtotime($user->created_at)));  
                    $shop_contrac_plan->save();
                    }

                    if($admins->free_shop == 0){
                        $store_invoice =  new Store_invoice();
                        $store_invoice->shop_id = $user->id; 
                        $store_invoice->plan = 'FREE';
                        $store_invoice->sub_total = '0.00';
                        $store_invoice->total = '0.00';
                        $store_invoice->status = 'inactive';
                        $store_invoice->payment = 'paid';
                        $store_invoice->due_date = date('Y-m-d', strtotime(+$admins->free_shop.'days', strtotime($user->created_at)));
                        $store_invoice->save();
    
                        $shop_contrac_plan = new  ShopContractedPlans();
                        $shop_contrac_plan->shop_id = $user->id;
                        $shop_contrac_plan->name_plan ='FREE';
                        $shop_contrac_plan->subscription_status = 'inactive';
                        $shop_contrac_plan->due_date  = date('Y-m-d', strtotime(+$admins->free_shop.'days', strtotime($user->created_at)));  
                        $shop_contrac_plan->save();
                        }



                }

                if($this->guard == 'supplier'){
                    Mail::to($user->email)->send(new ApprovedRegistration($user));

                    $admins = Admins::find(2);

                   
                    $supplier_invoice =  new Supplier_invoice();
                    $supplier_invoice->supplier_id  = $user->id; 
                    $supplier_invoice->plan = 'FREE';
                    $supplier_invoice->sub_total = '0.00';
                    $supplier_invoice->total = '0.00';
                    $supplier_invoice->status = 'active';
                    $supplier_invoice->payment = 'paid';
                    $supplier_invoice->due_date = date('Y-m-d', strtotime(+$admins->free_shop.'days', strtotime($user->created_at)));
                    $supplier_invoice->save();

                    $supplier_contrac_plan = new  SupplierContractedPlans();
                    $supplier_contrac_plan->supplier_id = $user->id;
                    $supplier_contrac_plan->name_plan ='FREE';
                    $supplier_contrac_plan->subscription_status = 'active';
                    $supplier_contrac_plan->due_date  = date('Y-m-d', strtotime(+$admins->free_shop.'days', strtotime($user->created_at)));  
                    $supplier_contrac_plan->save();                
                   

                  
                    



                }
                
                return (object)(['status' => 'success', 'message' => 'Você foi cadastrado com sucesso. Seja bem vindo ao '.config('app.name').'!']);
            }else{
                return (object)(['status' => 'error', 'message' => 'A senha e a confirmação de senha devem ser iguais.']);
            }
        }
    }

    public function logout(){
        Auth::guard($this->guard)->logout();
    }

    protected function createUser($name, $email, $password, $phone = NULL, $document = NULL){
        $user = new $this->model();

        if($this->guard == 'supplier'){ //caso seja fornecedor, gera um código pro nome e salva só o legal name
            do {
                $newName = rand(100000000, 999999999); //gera um número aleatório de no mínimo 9 dígitos
                $user->name = $newName;
            } while (Suppliers::where('name', $newName)->first());
            $user->legal_name = $name;
        }else{
            $user->name = $name;
        }
        
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->hash = $this->randomHash('hash');
        $user->private_hash = $this->randomHash('private_hash');
        $user->terms_agreed = 1;
        $user->status = 'active';
        $user->phone = $phone;
        $user->document = $document;

        if(!$user->save()){
            throw new CustomException("Erro ao cadastrar seu usuário. Tente novamente em alguns minutos.", 500);
        }

        return $user;
    }

    protected function randomHash($field){
        $hash = Str::random(30);

        $verify = $this->model::where($field, $hash)->first();

        if($verify){
            return $this->randomHash($field);
        }else{
            return $hash;
        }
    }

    public function passwordRecovery($email){
        $user = $this->model::where('email', $email)->first();

        if(!$user){
            return (object)(['status' => 'error', 'message' => 'Não há nenhum usuário cadastrado com este e-mail.']);
        }

        try {
            $user->password_recovery_hash = $this->randomHash('password_recovery_hash');
            $user->save();

            $url = route($this->guard.'.login.define_new_password', $user->password_recovery_hash);

            Mail::to($user->email)->send(new PasswordRecoveryMail($user, $url));

            return (object)(['status' => 'success', 'message' => 'Nós enviamos um link de recuperação de senha para seu email. Clique no link em seu email para definir sua nova senha.']);
        } catch (\Exception $e) {
            return (object)(['status' => 'error', 'message' => 'Aconteceu um erro inesperado ao recuperar sua senha, tente novamente em alguns minutos.']);
        }
    }

    public function defineNewPassword($hash, $password, $password_confirmation){
        if($password != $password_confirmation){
            return (object)(['status' => 'error', 'message' => 'A senha e a confirmação de senha devem ser iguais.']);
        }

        $user = $this->model::where('password_recovery_hash', $hash)->first();

        if(!$user){
            return (object)(['status' => 'error', 'message' => 'Este link já expirou. Gere um novo link de recuperação de senha para definir uma nova senha.']);
        }

        $user->password = Hash::make($password);
        $user->password_recovery_hash = null;

        if($user->save()){
            return (object)(['status' => 'success', 'message' => 'Senha redefinida com sucesso.']);
        }else{
            return (object)(['status' => 'error', 'message' => 'Aconteceu algum erro inesperado ao redefinir sua senha, tente novamente em alguns minutos.']);
        }
    }
}
