<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Http\Controllers\Shop\FunctionsController;

class VerifyShopPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //verifica se o lojista autenticado está com o plano em dias, senão estiver redireciona ele pra página de settings sempre
        //ainda está no plano gratuito, porém criou a conta a menos de 14 dias
        
        $shop = Auth::guard('shop')->user();
        
        // $dataVencimentoGratuito = date("Y-m-d", strtotime('+14 days', strtotime($shop->created_at))); //vencimento do plano gratuito
        // $dataAtual = date("Y-m-d");

        //caso ainda esteja no periodo gratuito LEGACY - Agora não tem período gratuito 26/10/2021
        // if(($dataAtual <= $dataVencimentoGratuito && $shop->token_card) || ($dataAtual <= $dataVencimentoGratuito && !$shop->token_card && $dataAtual <= "2021-08-20") || FunctionsController::freeShop($shop)){
        //     return $next($request);
        // }

        //só verifica se é um shop liberado
        if(FunctionsController::freeShop($shop)){
            return $next($request);
        }

        if( ((!$shop->contracted_plan || $shop->contracted_plan->subscription_status != 'Ativa') && (!$shop->internal_subscription || $shop->internal_subscription->status != 'active'))

            && $request->path() != "shop/settings"
            && $request->path() != "shop/plans"
            && $request->path() != "shop/logout"
            && $request->path() != "shop/profile"
            && $request->path() != "shop/profile/update"
            && $request->path() != "shop/settings/remove-free-card"
            && !$request->route()->named('shop.plans.selected')
            && !$request->route()->named('shop.plans.store')){

            //verifica se o usuário já selecionou o plano, caso tenha selecionado, redireciona ele pra pagina onde fornece os dados, já com o plano selecionado
            //e não tenha fornecido o cartão
            //if($shop->contracted_plan && $shop->contracted_plan->plan_id && !$shop->token_card){
            if($shop->contracted_plan && $shop->contracted_plan->plan_id){
                //dd('here');
                return redirect(route('shop.plans.selected', ['plan_id' => $shop->contracted_plan->plan_id]));
            }

            //verifica se o usuário já selecionou o plano INTERNO, caso tenha selecionado, redireciona ele pra pagina onde fornece os dados, já com o plano selecionado
            //e não tenha fornecido o cartão
            //if($shop->internal_subscription && $shop->internal_subscription->plan_id && !$shop->token_card){
            if($shop->internal_subscription && $shop->internal_subscription->plan_id){
                return redirect(route('shop.plans.selected', ['plan_id' => $shop->internal_subscription->plan_id]));
            }
            //dd($shop->contracted_plan);
            //redireciona
            return redirect(route('shop.plans.index'));
        }

        return $next($request);
    }
}
