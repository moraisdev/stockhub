<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/* Requests */
use App\Http\Requests\Supplier\Login\AuthenticateRequest;
use App\Http\Requests\Supplier\Login\RegisterRequest;

use App\Models\Shops;
use App\Models\IndicationShop;
use App\Models\AffiliateLink;
use App\Models\ShopContractedPlans;
use App\Models\TokenCardShop;
use App\Models\AccessesAffiliateLink;
use App\Models\InternalSubscriptionShop;
use App\Models\CouponInternalSubscriptionShop;
use App\Models\UsedCouponInternalSubscriptionShop;
use App\Models\Admins;
use App\Models\Store_invoice;
use App\Models\Categories;
use App\Models\Products;
use App\Models\ProductVariants;
use App\Models\ProductImages;


use App\Mail\Welcome;
use App\Mail\ApprovedRegistration;
use Mail;

/* Services */
use App\Services\LoginService;

use Str;

use Auth;
use App\Services\SafeToPayPlansService;
use App\Services\SafeToPayService;
use App\Models\ShopCanceledPlans;
use App\Models\PaymentInternalSubscriptionShop;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(){
        $this->loginService = new LoginService('shop');
    }

    public function index(Request $request){
        $redirect_url = url()->previous();
        $admins = Admins::find(2);

        if(Str::contains($redirect_url, 'login') || !Str::contains($redirect_url, 'shop')){
            $redirect_url = route('shop.dashboard'  );
        }        


        return view('shop.login.index', compact('redirect_url' , 'admins'));
    }

    public function authenticate(AuthenticateRequest $request){
        $authentication = $this->loginService->authenticate($request->email, $request->password, $request->keep_user_connected, $request);

        if($authentication->status == 'success'){
            //faz a atualização do plano do usuário antes dele entrar
            $shop = Auth::guard('shop')->user();
            
            

            //atualiza os dados do plano do usuário, pra não precisa ficar consultando o servidor do gateway de pagamento a cada requisição
                // $safe2pay = new SafeToPayPlansService();

                // $safe2pay->updateShopSubscription($shop);

            return redirect($request->redirect_url)->with(['success_notification' => $authentication->message]);
        }else{
            return redirect()->back()->with(['error' => $authentication->message])->withInput($request->except('password'));
        }
    }

    public function register(Request $request){
        $email = $request->input('email');

        //salva o cookie caso exista
        if(isset($request->ind) && $request->ind != ''){
            $tokenInd = $request->ind;
        }else{
            //caso tenha chegado pela página só pelo token, salva o acesso (token wordpress)
            $tokenInd = isset( $_COOKIE['_ind_mawa_register_wordpress'] ) ? $_COOKIE['_ind_mawa_register_wordpress'] : NULL;   
        }

        //verifica se é um token valido
        $link = AffiliateLink::where('token', $tokenInd)->first();
                
        if($link){ //caso seja um token valido, salva a indicacao
            Cookie::queue('_ind_mawa_register', $tokenInd, 10080); //salva o cookie no navegador com validade de uma semana
            
            AccessesAffiliateLink::create(['affiliate_links_id' => $link->id]);//salva também o acesso
        }

        return view('shop.login.register', compact('email'));
    }

    public function postRegister(RegisterRequest $request){
        $register = $this->loginService->register($request->name, $request->email, $request->password, $request->password_confirmation, $request->terms_agreed);
    
            


        if($register->status == 'success'){
            return redirect()->route('shop.login')->with(['success' => $register->message]);
        }else{
            return redirect()->back()->with(['error' => $register->message])->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    public function postRegisterJson(RegisterRequest $request){
        $register = $this->loginService->register($request->name, $request->email, $request->password, $request->password_confirmation, $request->terms_agreed, $request->phone, $request->document);        

        if($register->status == 'success'){
            //vincula o token ao usuário, caso exista
            $tokenInd = Cookie::get('_ind_mawa_register') ? Cookie::get('_ind_mawa_register') : NULL;
            
            if(!isset($tokenInd) || !$tokenInd || $tokenInd == ''){
                $tokenInd = isset( $_COOKIE['_ind_mawa_register_wordpress'] ) ? $_COOKIE['_ind_mawa_register_wordpress'] : NULL;
            }
            $shopNew = Shops::where('email', $request->email)->first();

            if($tokenInd){
                $link = AffiliateLink::where('token', $tokenInd)->first();
                
                if($link){ //caso seja um token valido, salva a indicacao
                    if($shopNew){
                        IndicationShop::create([
                            'affiliate_links_id' => $link->id,
                            'shop_id' => $shopNew->id
                        ]);
                    }
                }
            }

            if($shopNew){
                //salva o id do usuáio criptografado do usuário logado para ele escolher o plano agora
                session(['_mawa_new_user' => $shopNew->id]);
            }

            return response()->json(['msg' => $register->message], 200);
        }else{
            return response()->json(['msg' => $register->message], 400);
        }
    }

    public function postRegisterShopPlanJson(Request $request){
        //salva o plano selecionado pro usuário na session
        $shopId = session('_mawa_new_user');
        
        if($shopId){
            //registar o plano mensal normal, caso contrário, registra um plano interno
            if($request->plan_id == 7734){
                $shopContractedPlan = ShopContractedPlans::firstOrCreate([
                    'shop_id' => $shopId
                ]);
        
                $shopContractedPlan->plan_id = $request->plan_id;
        
                if($shopContractedPlan->save()){
                    return response()->json(['msg' => 'Plano registrado com sucesso.'], 200);
                }
            }else{ //é um plano mensal ou anual
                //cria uma assinatura interna
                if($request->plan_id == 7736 || $request->plan_id == 7739){
                    $internalSubscriptionShop = InternalSubscriptionShop::firstOrCreate([
                        'shop_id' => $shopId
                    ]);
            
                    $internalSubscriptionShop->plan_id = $request->plan_id;
            
                    if($internalSubscriptionShop->save()){
                        return response()->json(['msg' => 'Plano registrado com sucesso.'], 200);
                    }
                }
            }
        }
        
        return response()->json(['msg' => 'Erro ao registrar plano'], 400);
    }

    public function postRegisterShopCardJson(Request $request){
        $holder = $request->holder;
        $cardNumber = $request->card_number;
        $expirationDate = $request->expiration_date;
        $securityCode = $request->security_code;
        $numberInstallments = $request->number_installments;
        $coupon = $request->coupon;

        //erros
        if(!$holder || $holder == ''){ return response()->json(['msg' => 'Nome inválido'], 400); }
        if(!$cardNumber || $cardNumber == ''){ return response()->json(['msg' => 'Número do cartão inválido'], 400); }
        if(!$expirationDate || $expirationDate == ''){ return response()->json(['msg' => 'Data de expiração inválida'], 400); }
        if(!$securityCode || $securityCode == ''){ return response()->json(['msg' => 'Código de segurança inválido'], 400); }
        if(!$numberInstallments || $numberInstallments == ''){ return response()->json(['msg' => 'Número de parcelas inválido'], 400); }

        $shopId = session('_mawa_new_user');

        if($shopId){
            ///tokeniza o cartão e salva na safe2pay e na mawa
            $safe2pay = new SafeToPayService();

            $tokenCard = $safe2pay->tokenizeCard($holder, $cardNumber, $expirationDate, $securityCode);
            
            if($tokenCard){
                $tokenCardShop = TokenCardShop::create(['shop_id' => $shopId]);
                $tokenCardShop->token = $tokenCard;
                $tokenCardShop->last_digits = substr($request->card_number, -4); //salva os ultimos 4 digitos para identificação do cartão
                $tokenCardShop->installments = $numberInstallments;

                if($tokenCardShop->save()){
                    //caso tenha um cupom, salva também
                    $coupon = CouponInternalSubscriptionShop::where('code', $coupon)
                                            ->first();
                    if($coupon){ //se for um cupom válido
                        UsedCouponInternalSubscriptionShop::firstOrCreate([
                            'coupon_internal_subscription_shop_id' => $coupon->id,
                            'token_card_id' => $tokenCardShop->id,
                            'shop_id' => $shopId
                        ]);
                    }

                    $shop = Shops::find($shopId);

                    if($shop){
                         //realiza a cobrança direto
                        $safe2pay = new SafeToPayPlansService();

                        $planos = $safe2pay->getShopPlans();
                        $errorMsg = '';
                        //ja realiza a compra então

                        if($shop->token_card && ($shop->contracted_plan && $shop->contracted_plan->plan_id && $shop->contracted_plan->plan_id == 7734 && !$shop->contracted_plan->subscription) && !$shop->internal_subscription){
                            //quer dizer que é só pegar o plano escolhido e comprar direto
                            $subscriptionResponse = $safe2pay->storeShopSubscriptionTokenCard($shop, $shop->token_card->token, $shop->contracted_plan->plan_id);

                            if($subscriptionResponse['hasError']) {
                                return response()->json(['msg' => $subscriptionResponse['error'] ], 400);
                            }
                                
                            if($subscriptionResponse && $subscriptionResponse['status'] == 'Ativa'){ //caso tenha retornado os dados corretamente
                                //cria ou edita o plano do usuário
                                $shopContractedPlan = ShopContractedPlans::firstOrCreate([
                                    'shop_id' => $shop->id
                                ]);
                    
                                $shopContractedPlan->plan_id = $shop->contracted_plan->plan_id;
                                $shopContractedPlan->subscription = $subscriptionResponse['subscription'];
                                $shopContractedPlan->transaction = $subscriptionResponse['transaction'];
                                $shopContractedPlan->subscription_status = $subscriptionResponse['status'];
                    
                                if($shopContractedPlan->save()){
                                    $shop->status = 'active'; //muda o status de alguma assinatura antiga (legacy)
                                    $shop->save();
                    
                                    //verifica se tem algum cancelamento de plano agendado, se tiver apaga
                                    $canceledPlans = ShopCanceledPlans::where('shop_id', $shop->id)
                                                                    ->where('status', 'pending')
                                                                    ->get();
                                    //apaga todos
                                    foreach ($canceledPlans as $canceledPlan) {
                                        $canceledPlan->delete();
                                    }
                                    Mail::to($shop->email)->send(new Welcome($shop));
                                    Mail::to($shop->email)->send(new ApprovedRegistration($shop));
                                    return response()->json(['msg' => 'Cartão salvo com sucesso.'], 200);
                                    //return redirect()->route('shop.settings.index')->with('success', 'Plano atualizado com sucesso.');
                                }
                            }
                        }

                        //faz a mesma coisa pro plano interno
                        if($shop->token_card && ($shop->internal_subscription && $shop->internal_subscription->plan_id && (!$shop->internal_subscription->status || $shop->internal_subscription->status != 'active') ) ){
                            //quer dizer que é só pegar o plano escolhido e comprar direto
                            
                            //verifica se o usuário utilizou algum cupom
                            $product = self::getInternalPlanData($shop->internal_subscription->plan_id, $shop->token_card->used_coupon ? $shop->token_card->used_coupon->coupon_internal_subscription_shop_id : NULL);
                            
                            $subscriptionResponse = $safe2pay->storeShopInternalSubscriptionV2($shop, $product);
                            
                            if($subscriptionResponse['hasError']) {
                                return response()->json(['msg' => $subscriptionResponse['error'] ], 400);
                            }
                                
                            if($subscriptionResponse && $subscriptionResponse['status'] == 'Ativa'){ //caso tenha retornado os dados corretamente
                                //cria ou edita o plano do usuário
                                $shopInternalSubscription = InternalSubscriptionShop::firstOrCreate([
                                    'shop_id' => $shop->id
                                ]);

                                $shopInternalSubscription->plan_id = $shop->internal_subscription->plan_id;
                                $shopInternalSubscription->status = 'active';

                                if($shopInternalSubscription->save()){
                                    //cria o objeto do pagamento com a transaction respectiva pra ter um histórico de pagamentos
                                    PaymentInternalSubscriptionShop::create([
                                        'internal_subscription_shop_id' => $shopInternalSubscription->id,
                                        'status' => $subscriptionResponse['status'],
                                        'transaction_id' => $subscriptionResponse['transaction'],
                                        'payment_json' => $subscriptionResponse['resultJson'],
                                    ]);

                                    $shop->status = 'active'; //muda o status de alguma assinatura antiga (legacy)
                                    $shop->save();

                                    //verifica se tem algum cancelamento de plano agendado, se tiver apaga
                                    $canceledPlans = ShopCanceledPlans::where('shop_id', $shop->id)
                                                                    ->where('status', 'pending')
                                                                    ->get();
                                    //apaga todos
                                    foreach ($canceledPlans as $canceledPlan) {
                                        $canceledPlan->delete();
                                    }

                                    Mail::to($shop->email)->send(new Welcome($shop));
                                    Mail::to($shop->email)->send(new ApprovedRegistration($shop));
                                    return response()->json(['msg' => 'Cartão salvo com sucesso.'], 200);
                                    //return redirect()->route('shop.settings.index')->with('success', 'Plano atualizado com sucesso.');
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return response()->json(['msg' => $errorMsg != '' ? $errorMsg : 'Erro ao salvar cartão'], 400);
    }

    public function forgotPassword(){
        return view('shop.login.forgot_password');
    }

    public function postForgotPassword(Request $request){
        $email = $request->email;

        $recovery = $this->loginService->passwordRecovery($email);

        if($recovery->status == 'success'){
            return redirect()->route('shop.login')->with(['success' => $recovery->message]);
        }else{
            return redirect()->back()->with(['error' => $recovery->message])->withInput($request->only(['email']));
        }
    }

    public function defineNewPassword($hash){
        $shop = Shops::where('password_recovery_hash', $hash)->first();

        if(!$shop){
            return redirect()->route('shop.login.forgot_password')->with('error', 'Este link já expirou. Gere um novo link de recuperação de senha para definir uma nova senha..');
        }

        return view('shop.login.define_new_password', compact('hash'));
    }

    public function postDefineNewPassword($hash, Request $request){
        $new_password = $this->loginService->defineNewPassword($hash, $request->password, $request->password_confirmation);

        if($new_password->status == 'success'){
            return redirect()->route('shop.login')->with(['success' => $new_password->message]);
        }else{
            return redirect()->back()->with(['error' => $new_password->message]);
        }
    }

    public function logout(){
        $this->loginService->logout();

        return redirect()->route('shop.login');
    }

    //isso aqui é pressa, refatorar dps
    protected static function getInternalPlanData($planId, $couponId = NULL){
        $planDescription = "";
        $planValue = 0.0;
        $discountValue = 0.0;

        if($couponId){ //verifica se foi passado o cupom
            $coupon = CouponInternalSubscriptionShop::find($couponId);

            if($coupon && $planId == 7739){ //verifica se é o plano anual
                $discountValue = $coupon->value;
            }
        }

        if($planId == 7739){
            $planDescription = "Plano ".env('APP_NAME', 'Laravel')." para o Lojista Anual";
            $planValue = $discountValue > 0.0 ? $discountValue : 923.70; //caso tenha sido aplicado o desconto, cobra o valor do desconto
        }

        if($planId == 7736){
            $planDescription = "Plano ".env('APP_NAME', 'Laravel')." para o Lojista Semestral";
            $planValue = 539.40;
        }

        $product = (object)[
            'code' => $planId,
            'name' => $planDescription,
            'value' => $planValue,
            'quantity' => 1
        ];

        return $product;
    }

    public function catolog(){  

        $admins = Admins::find(2);
        
        $products_ids = [];
        $categories = Categories::get();
        $products  = Products::with('variants', 'supplier')->whereHas('supplier', function($q){
		    $q->where('status', 'active')->where('login_status', 'authorized');
        })->where('public', 1)->whereNotIn('id', $products_ids)->get();
       
        if($admins->catalogo == 1){
            return redirect()->back()->with('error', 'O Catalogo so esta disponivel para assinantes');

        }else{
            return view('shop.catalog.catalog', compact('categories' , 'products' , 'admins' ));
        } 
        

        
    }

    public function produtodetalhe($id){  

        $admins = Admins::find(2);
        $categories = Categories::get();
        $products  = Products::where('id' , $id)->first();
        $productvar = ProductVariants::where('product_id' , $id)->first();
        $productimg = ProductImages::where('product_id' , $id)->get();
      // dd($productvar);
      

        return view('shop.catalog.detalhe', compact('categories' , 'products' , 'productimg' ,'productvar' , 'admins' ));
    }

    public function produtocategoria($id){  

        $admins = Admins::find(2);
        $products_ids = [];
        $categories = Categories::get();
        $products  = Products::with('variants', 'supplier')->whereHas('supplier', function($q){
		    $q->where('status', 'active')->where('login_status', 'authorized');
        })->where('public', 1)->where('category_id', $id)->whereNotIn('id', $products_ids)->get();
       
        

        return view('shop.catalog.categoria', compact('categories' , 'products' , 'admins' ));
      // dd($productvar);
      

    }

}
