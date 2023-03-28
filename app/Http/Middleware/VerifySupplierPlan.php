<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

use App\Services\SupplierPlanService;

class VerifySupplierPlan
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
        //verifica se o fornecedor autenticado está com o plano em dias, senão estiver redireciona ele pra página de settings sempre
        $supplier = Auth::guard('supplier')->user();

        $supplierPlanService = new SupplierPlanService($supplier);
        $nexPlan = number_format($supplierPlanService->getActualPlanValue(), 2, ',', '.');
        
        //caso ainda esteja no plano gratuito
        if(!$supplier->contracted_plan && $nexPlan == 0){
            return $next($request);
        }

        if((!$supplier->contracted_plan || $supplier->contracted_plan->subscription_status != 'Ativa')
            && $request->path() != "supplier/settings"
            && $request->path() != "supplier/plans"
            && $request->path() != "supplier/logout"
            && !$request->route()->named('supplier.plans.selected')
            && !$request->route()->named('supplier.plans.store')){
            //redireciona
            return redirect(route('supplier.settings.index'));
        }

        return $next($request);
    }
}
