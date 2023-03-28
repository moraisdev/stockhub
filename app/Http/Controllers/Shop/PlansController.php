<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Services\SafeToPayPlansService;
use App\Services\SafeToPayService;
use Auth;
use App\Models\ShopContractedPlans;
use App\Models\ShopCanceledPlans;
use App\Models\TokenCardShop;
use App\Models\InternalSubscriptionShop;
use App\Models\PaymentInternalSubscriptionShop;
use App\Models\CouponInternalSubscriptionShop;
use App\Models\UsedCouponInternalSubscriptionShop;
use App\Models\Admins;
use Illuminate\Support\Facades\Log;
use App\Models\Shopplano;
use App\Models\Store_invoice;
use App\Services\Gerencianetpay;

class PlansController extends Controller
{
    public function index(){
        $shop = Auth::guard('shop')->user();

        $admin = Admins::find(2);

        $planos = Shopplano::all();
      
                
        if ($admin->plano_shop == '2') {
            $safe2pay = new SafeToPayPlansService();

            $planos = $safe2pay->getShopPlans();
    
        

        //caso tenha vencido o plano e ele forneceu o cartão e ja tenha escolhido o plano e a assinatura seja nula
        //ou seja, forneceu os dados certinho porém não efetivou a compra da assinatura
        //ja realiza a compra então
        $dataVencimentoGratuito = date("Y-m-d", strtotime('+14 days', strtotime($shop->created_at))); //vencimento do plano gratuito
        $dataAtual = date("Y-m-d");

        if($shop->token_card && ($shop->contracted_plan && $shop->contracted_plan->plan_id && $shop->contracted_plan->plan_id == 7734 && !$shop->contracted_plan->subscription) && $dataAtual > $dataVencimentoGratuito && !$shop->internal_subscription){
            //quer dizer que é só pegar o plano escolhido e comprar direto
            $subscriptionResponse = $safe2pay->storeShopSubscriptionTokenCard($shop, $shop->token_card->token, $shop->contracted_plan->plan_id);
                
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
    
                    return redirect()->route('shop.settings.index')->with('success', 'Plano atualizado com sucesso.');
                }
            }
            $msgError = '';
            if(!$shop->document){ $msgError.="O CPF/CNPJ é inválido. "; }
            if(!$shop->address){ $msgError.="É necessário informar os dados de endereço. "; }

            return redirect(route('shop.profile'))->with('error', 'Erro ao atualizar plano. '.$msgError);
        }

        //faz a mesma coisa pro plano interno
        if($shop->token_card && ($shop->internal_subscription && $shop->internal_subscription->plan_id && (!$shop->internal_subscription->status || $shop->internal_subscription->status != 'active') ) && $dataAtual > $dataVencimentoGratuito){
            //quer dizer que é só pegar o plano escolhido e comprar direto
            
            //verifica se o usuário utilizou algum cupom
            $product = self::getInternalPlanData($shop->internal_subscription->plan_id, $shop->token_card->used_coupon ? $shop->token_card->used_coupon->coupon_internal_subscription_shop_id : NULL);

            $subscriptionResponse = $safe2pay->storeShopInternalSubscriptionV2($shop, $product);
                
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

                    return redirect()->route('shop.settings.index')->with('success', 'Plano atualizado com sucesso.');
                }
            }
            $msgError = '';
            if(!$shop->document){ $msgError.="O CPF/CNPJ é inválido. "; }
            if(!$shop->address){ $msgError.="É necessário informar os dados de endereço. "; }

            return redirect(route('shop.profile'))->with('error', 'Erro ao atualizar plano. '.$msgError);
        }



     
    }
    if ($admin->plano_shop == '1') {


        return view('shop.plans.index', compact('planos'));

    }
    if ($admin->plano_shop == '0') {
        return redirect()->route('shop.plans.invoice')->with('success', 'Todos os Planos para Lojista e Gratuito .');


    }
      

}   


 
    


   
   


    public function selectedPlan(Request $request){
        $shop = Auth::guard('shop')->user();

        $admin = Admins::find(2);

        $planos = Shopplano::all();
        
      
                
        if ($admin->plano_shop == '2') {
        //carrega os dados do plano
        $safe2pay = new SafeToPayPlansService();

        $plano = $safe2pay->getShopPlan($request->plan_id);

        //antes verifica se é o caso daqueles usuários que já cadastraram o cartão porém não escolheram o plano
        //caso seja um desses usuários, já faz a compra
        if($plano){
            $dataVencimentoGratuito = date("Y-m-d", strtotime('+14 days', strtotime($shop->created_at))); //vencimento do plano gratuito
            $dataAtual = date("Y-m-d");

            if($shop->token_card && !$shop->contracted_plan && $dataAtual > $dataVencimentoGratuito && !$shop->internal_subscription){
                //caso tenha um token cadastrado, caso não tenha escolhido o plano e caso o plano gratuito já tenha vencido
                //quer dizer que é só pegar o plano escolhido e comprar direto
                $subscriptionResponse = $safe2pay->storeShopSubscriptionTokenCard($shop, $shop->token_card->token, $request->plan_id);
                
                if($subscriptionResponse && $subscriptionResponse['status'] == 'Ativa'){ //caso tenha retornado os dados corretamente
                    //cria ou edita o plano do usuário
                    $shopContractedPlan = ShopContractedPlans::firstOrCreate([
                        'shop_id' => $shop->id
                    ]);
        
                    $shopContractedPlan->plan_id = $request->plan_id;
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
        
                        return redirect()->route('shop.settings.index')->with('success', 'Plano atualizado com sucesso.');
                    }
                }
                $msgError = '';
                if(!$shop->document){ $msgError.="O CPF/CNPJ é inválido. "; }
                if(!$shop->address){ $msgError.="É necessário informar os dados de endereço. "; }

                return redirect(route('shop.profile'))->with('error', 'Erro ao atualizar plano. '.$msgError);
            }
            
            if($plano->Id == 7734 || $plano->Id == 7736 || $plano->Id == 7739){
                //plano semestral
                $codeSelectedPlan = "";

                if($plano->Id == 7736){
                    $codeSelectedPlan = "
                    <div class='row' id='installment-plans'>
                        <div class='col-md-3'>
                            <div class='form-group mb-3'>
                                <label for='select-installment-plan'>Número de Parcelas</label>
                                <select class='form-control' id='select-installment-plan-semiannual' name='installments'>
                                    <option value='1'>1</option>
                                    <option value='2'>2</option>
                                    <option value='3'>3</option>
                                    <option value='4'>4</option>
                                    <option value='5'>5</option>
                                    <option value='6'>6</option>                                                    
                                </select>
                            </div>
                        </div>
                        <div class='col-md-3 mt-5'>
                            <div class='form-group mb-3' id='value-plan-semiannual'>
                                <h3>1x de <b>R$ 539,40</b></h3>
                            </div>
                        </div>
                    </div>
                    ";
                }

                //plano anual
                if($plano->Id == 7739){
                    $codeSelectedPlan = "
                    <div class='row' id='installment-plans'>
                        <div class='col-md-3'>
                            <div class='form-group mb-3'>
                                <label for='select-installment-plan'>Número de Parcelas</label>
                                <select class='form-control' id='select-installment-plan-annual' name='installments'>
                                    <option value='1'>1</option>
                                    <option value='2'>2</option>
                                    <option value='3'>3</option>
                                    <option value='4'>4</option>
                                    <option value='5'>5</option>
                                    <option value='6'>6</option>
                                    <option value='7'>7</option>                                                   
                                    <option value='8'>8</option>
                                    <option value='9'>9</option>
                                    <option value='10'>10</option>
                                    <option value='11'>11</option>
                                    <option value='12'>12</option>
                                </select>
                            </div>                                            
                        </div>
                        <div class='col-md-3 mt-5'>
                            <div class='form-group mb-3' id='value-plan-annual'>
                                <h3>1x de <b>R$ 923,70</b></h3>
                            </div>
                        </div>
                    </div>
                    ";
                }

                return view('shop.plans.selected', compact('plano', 'codeSelectedPlan'));
            }
        }
    } 
    if ($admin->plano_shop == '1') {

        $plano = Shopplano::where('id' , $request->plan_id)->first();       
        $shop_contact_plano = ShopContractedPlans::where('shop_id', $shop->id)->first();

        if ($shop_contact_plano->name_plan == 'FREE'){
            $shop_invoice = new Store_invoice();
            $shop_invoice->shop_id = $shop->id;
            $shop_invoice->plan = $plano->descricao;
            $shop_invoice->sub_total = $plano->valor; 
            $shop_invoice->total = $plano->valor;
            $shop_invoice->status = 'active';
            $shop_invoice->payment = 'pending';
            $shop_invoice->due_date = $shop_contact_plano->due_date;
            $shop_invoice->save();             
        }

        $shop_contact_plano->name_plan = $plano->descricao;
        $shop_contact_plano->plan_id =$plano->id;
        $shop_contact_plano->valor = $plano->valor;
        $shop_contact_plano->save();
        $codeSelectedPlan = 1;
        
        return redirect()->route('shop.plans.invoice')->with('success', 'Plano Alterado com sucesso.');

       

    }   
         

       return redirect(route('shop.dashboard'))->with('error', 'Erro ao carregar dados do plano. ');
    }
    
    public function store(Request $request){
        $shop = Auth::guard('shop')->user();
        try {
            if($request->plan_id == 7736 || $request->plan_id == 7739){ //caso seja o plano semestral ou anual
                //dd('ere');
                //faz a requisição para realizar o pagamento na quantidade de parcelas especificadas
    
                if(!$shop->token_card){
                    //antes, tokeniza o cartão pra vir fazer a cobrança posteriormente
                    ///tokeniza o cartão e salva na safe2pay e na mawa
                    $holder = $request->holder;
                    $cardNumber = $request->card_number;
                    $expirationDate = $request->expiration_date;
                    $securityCode = $request->security_code;
                    $numberInstallments = $request->installments;
    
                    //erros
                    if(!$holder || $holder == ''){ return redirect()->back()->with('error', 'Nome inválido'); }
                    if(!$cardNumber || $cardNumber == ''){ return redirect()->back()->with('error', 'Número do cartão inválido'); }
                    if(!$expirationDate || $expirationDate == ''){ return redirect()->back()->with('error', 'Data de expiração inválida'); }
                    if(!$securityCode || $securityCode == ''){ return redirect()->back()->with('error', 'Código de segurança inválido'); }
                    if(!$numberInstallments || $numberInstallments == ''){ return redirect()->back()->with('error', 'Número de parcelas inválido'); }
    
                    $safe2pay = new SafeToPayService();
                    
                    $tokenCard = $safe2pay->tokenizeCard($holder, $cardNumber, $expirationDate, $securityCode);
    
                    if($tokenCard){
                        $tokenCardShop = TokenCardShop::create(['shop_id' => $shop->id]);
                        $tokenCardShop->token = $tokenCard;
                        $tokenCardShop->last_digits = substr($request->card_number, -4); //salva os ultimos 4 digitos para identificação do cartão
                        $tokenCardShop->installments = $numberInstallments;
        
                        $tokenCardShop->save();
                    }
                }else{
                    $tokenCard = $shop->token_card;
                }
                
                
                if($tokenCard){
                    //dd($tokenCard);
                    //faz o pagamento do plano escolhido
                    $safe2pay = new SafeToPayPlansService();
    
                    //verifica se tem cupom
                    $coupon = NULL;
                    if($request->coupon_code){
                        $coupon = CouponInternalSubscriptionShop::find($request->coupon_code);
    
                        if($coupon){ //se for um cupom válido
                            UsedCouponInternalSubscriptionShop::firstOrCreate([
                                'coupon_internal_subscription_shop_id' => $coupon->id,
                                'token_card_id' => $tokenCardShop->id,
                                'shop_id' => $shop->id
                            ]);
                        }
                    }                    
    
                    $product = self::getInternalPlanData($request->plan_id, $coupon ? $coupon->id : NULL);
                    
                    $subscriptionResponse = $safe2pay->storeShopInternalSubscription($request, $tokenCard->token, $product);
                    //dd($subscriptionResponse);
                    if($subscriptionResponse && $subscriptionResponse['status'] == 'Ativa'){ //caso tenha retornado os dados corretamente
                        //cria ou edita o plano do usuário
                        $shopInternalSubscription = InternalSubscriptionShop::firstOrCreate([
                            'shop_id' => $shop->id
                        ]);
        
                        $shopInternalSubscription->plan_id = $request->plan_id;
                        $shopInternalSubscription->status = 'active';
                        //$shopInternalSubscription->transaction = $subscriptionResponse['transaction'];
        
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
    
                            //verifica se tem alguma assinatura mensal, se tiver apaga também
                            $shopContractedPlan = ShopContractedPlans::where('shop_id', $shop->id)->first();
                            if($shopContractedPlan){
                                $shopContractedPlan->delete();
                            }
                            
        
                            return redirect()->route('shop.settings.index')->with('success', 'Plano atualizado com sucesso.');
                        }
                    }
                    
                    return redirect()->back()->with('error', 'Erro ao atualizar plano.');
                }
    
            }else{
                //faz a requisição para cadastrar a nova subscrição no plano
                $safe2pay = new SafeToPayPlansService();
                $subscriptionResponse = $safe2pay->storeShopSubscription($shop, $request);
                if($subscriptionResponse && $subscriptionResponse['status'] == 'Ativa'){ //caso tenha retornado os dados corretamente
                    //cria ou edita o plano do usuário
                    $shopContractedPlan = ShopContractedPlans::firstOrCreate([
                        'shop_id' => $shop->id
                    ]);
    
                    $shopContractedPlan->plan_id = $request->plan_id;
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
    
                        //verifica se tem alguma assinatura interna, se tiver apaga também
                        $shopInternalSubscription = InternalSubscriptionShop::where('shop_id', $shop->id)->first();
                        if($shopInternalSubscription){
                            $shopInternalSubscription->delete();
                        }                    
    
                        return redirect()->route('shop.settings.index')->with('success', 'Plano atualizado com sucesso.');
                    }
                }
                
                return redirect()->back()->with('error', 'Erro ao atualizar plano.');
            }
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Erro ao atualizar plano.');
        }
        
        
    }

    public function cancel(){
        return view('shop.plans.cancel');
    }

    public function storeCancel(Request $request){
        $shop = Auth::guard('shop')->user();
        $safe2pay = new SafeToPayPlansService();

        //cancelamento do plano mensal
        if($shop->contracted_plan && $shop->contracted_plan->plan_id && $shop->contracted_plan->plan_id == 7734){
            //pega os dados do vencimento
            $planoShop = $safe2pay->getShopSubscription($shop->contracted_plan->subscription); //pega os dados do plano do shop atual, passa o id da Subscription
            //dd($planoShop);
            $dataPagamento = $planoShop->SubscriptionCharges[0]->ChargeDate;
            $vencimentoPlano = date("d/m/Y", strtotime('+1 days', strtotime($dataPagamento))); //coloquei 29 dias só pq eu acho que no 30º dia ele vai cobrar novamente no cartão
            $vencimentoPlano = str_replace("/", "-", $vencimentoPlano);
            //dd(date("d/m/Y", strtotime('+0 days', strtotime($dataPagamento))));
            
            //cria a solicitação de cancelamento        
            $shopCanceledPlan = ShopCanceledPlans::firstOrCreate(['shop_id' => $shop->id]);
            
            $shopCanceledPlan->plan_id = $shop->contracted_plan->plan_id; //id do plano cancelado
            $shopCanceledPlan->subscription = $shop->contracted_plan->subscription; //assinatura que está sendo cancelada
            $shopCanceledPlan->status = 'pending'; //status como pending, pq ela só muda para executed dps do cronjob fazer a mudança na data especificada
            
            $shopCanceledPlan->change_date = date('Y-m-d', strtotime($vencimentoPlano)); //data em que o plano passará para gratuito
            
            $optionCancel = $request->option_cancel ? $request->option_cancel : '';

            $shopCanceledPlan->reason_cancellation = ($request->reason_cancellation ? $optionCancel.' - '.$request->reason_cancellation : $optionCancel);

            if($shopCanceledPlan->save()){
                //cancela a assinatura na safe2pay automaticamente
                if($safe2pay->cancelShopSubscription($shop)){
                    $shop->canceled_plan->status = 'executed';
                    $shop->canceled_plan->save();
                    
                    //salva o cancelamento da assinatura
                    $shop->contracted_plan->delete();
                    if($shop->token_card){
                        $shop->token_card->delete();
                    }

                    return redirect()->route('shop.settings.index')->with('success', 'Plano cancelado com sucesso.');
                }
            }
        }

        //cancelamento do plano anual e semestral
        if($shop->internal_subscription && $shop->internal_subscription->plan_id){
            //pega os dados do vencimento
            $lastPaymentInternalSubscription = PaymentInternalSubscriptionShop::where('internal_subscription_shop_id', $shop->internal_subscription->id)
                                                                                ->orderBy('id', 'desc')
                                                                                ->first();

            if($lastPaymentInternalSubscription){
                $dataPagamento = $lastPaymentInternalSubscription->created_at;

                $vencimentoPlano = date("d/m/Y", strtotime('+1 days', strtotime($dataPagamento))); //coloquei 29 dias só pq eu acho que no 30º dia ele vai cobrar novamente no cartão
                $vencimentoPlano = str_replace("/", "-", $vencimentoPlano);
                
                //cria a solicitação de cancelamento        
                $shopCanceledPlan = ShopCanceledPlans::firstOrCreate(['shop_id' => $shop->id]);
                
                $shopCanceledPlan->plan_id = $shop->internal_subscription->plan_id; //id do plano cancelado
                $shopCanceledPlan->subscription = 'internal_'.$shop->internal_subscription->id; //assinatura que está sendo cancelada
                $shopCanceledPlan->status = 'pending'; //status como pending, pq ela só muda para executed dps do cronjob fazer a mudança na data especificada
                
                $shopCanceledPlan->change_date = date('Y-m-d', strtotime($vencimentoPlano)); //data em que o plano passará para gratuito
                
                $optionCancel = $request->option_cancel ? $request->option_cancel : '';

                $shopCanceledPlan->reason_cancellation = ($request->reason_cancellation ? $optionCancel.' - '.$request->reason_cancellation : $optionCancel);

                if($shopCanceledPlan->save()){
                    //salva o cancelamento da assinatura
                    $shop->contracted_plan->delete();
                    if($shop->token_card){
                        $shop->token_card->delete();
                    }

                    return redirect()->route('shop.settings.index')->with('success', 'Plano cancelado com sucesso.');
                }
            }
                        
        }
        
        return redirect()->back()->with('error', 'Erro ao cancelar plano.');
    }

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
            $planDescription = "Plano ".config('app.name')." para o Lojista Anual";
            $planValue = $discountValue > 0.0 ? $discountValue : 923.70; //caso tenha sido aplicado o desconto, cobra o valor do desconto
        }

        if($planId == 7736){
            $planDescription = "Plano ".config('app.name')." para o Lojista Semestral";
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

    public function invoice(){

        $shop = Auth::guard('shop')->user();
        $shop_invoice = ShopContractedPlans::where('shop_id' , $shop->id)->orderBy('id', 'desc')->first();
        $shop_invoice_all = Store_invoice::where('shop_id' , $shop->id)->orderBy('id', 'desc')->get();
        
        
        return view('shop.plans.shop_invoice' , compact('shop_invoice', 'shop_invoice_all'));


    } 

    public function paymentdetail($id){


    }

    public function paymentpay($id)
    {
        $shop = Auth::guard('shop')->user();
        $shop_invoice = Store_invoice::where('id' , $id)->orderBy('id', 'desc')->first();
        $admins = Admins::find(2);
      
        return view('shop.plans.payment_plan' , compact('shop_invoice' , 'admins' ));
        

    }

    public function planspay($id, Request $request){

        
        $admins = Admins::find(2);
        $shop = Auth::guard('shop')->user();    
        $shop_invoice = Store_invoice::where('id' , $id)->first();

       

        if (isset($admins->geren_cliente_id) and $admins->geren_cliente_id != null ){
        
            if ($shop->responsible_name === null){
               return redirect()->back()->with(['error' => 'Cadastro do Perfil  não esta completo erro no nome.']);
   
           }
           
           if ($shop->document  === null){
               return redirect()->back()->with(['error' => 'Cadastro do Perfil  não esta completo erro cpf/cnpj.']);
   
           }
        }

        $metododepg = $request->payment_method;
        $gerencianet = new Gerencianetpay();      


        if($metododepg == 'pix'){
            $teste = $gerencianet->payplanshoppix($metododepg , $shop_invoice , $shop ,$admins );
            return redirect()->back()->with([$teste['status'] => $teste['message']]);


        }elseif ($metododepg == 'boleto'){
            $teste = $gerencianet->payplanshopboleto($metododepg , $shop_invoice , $shop,$admins  );
           
           
         return redirect()->back()->with([$teste['status'] => $teste['message']]);


        }


    }

    public function pay_plano_consulta(Request $request){
       
        $id = $request->id;
        $shop = Auth::guard('shop')->user();
        $plan_invoice = Store_invoice::where('id' , $request->id)->first();
        $admins = Admins::where('id' , 2)->first(); 
       // $json['id'] = $admins->geren_cliente_id;
       // return response()->json($json);
     
        $gerencianet = new Gerencianetpay(); 
           
           if($plan_invoice->status_pix == 1){

            $const_pay = $gerencianet->consultapixplano($admins , $plan_invoice );
            if (($const_pay == 'CONCLUIDA') and ($plan_invoice->payment == 'pending') ){

                $plan_invoice->status = 'active';
                $plan_invoice->payment = 'paid';
                $plan_invoice->date_payment = date("Y-m-d");
                $plan_invoice->save(); 
                                   
               }
       

           
        
        }    
        $json['id'] = $const_pay;
        return response()->json($const_pay);  


        }  
     

}
