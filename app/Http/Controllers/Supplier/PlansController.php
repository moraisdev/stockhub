<?php

namespace App\Http\Controllers\Supplier;

use Illuminate\Http\Request;

use App\Services\SafeToPayPlansService;
use Auth;
use App\Models\SupplierContractedPlans;
use App\Models\SupplierCanceledPlans;
use App\Models\Supplier_invoice;
use App\Services\Gerencianetpay;
use App\Models\Admins;
use App\Models\Fornecedorplano;
use App\Models\Suppliers;

class PlansController extends Controller
{
    public function index(){
       
        $admins = Admins::find(2);
       
        if ($admins->plano_f == 2){
        $safe2pay = new SafeToPayPlansService();

        $planos = $safe2pay->getSupplierPlans();

        return view('supplier.plans.index', compact('planos'));
        }elseif ($admins->plano_f == 1){
           
            $planos = Fornecedorplano::all();

            return view('supplier.plans.indexplan', compact('planos'));
       

        }elseif ($admins->plano_f == 0){

            return redirect()->route('supplier.plans.invoice')->with('success', 'Todos os Planos para Fornecedor e Gratuito .');

        } 
  
    }

    public function selectedPlan(Request $request){
        //carrega os dados do plano

        $admins = Admins::find(2);
        $supplier = Auth::guard('supplier')->user();
      
       
        if ($admins->plano_f == 2){
        
            $safe2pay = new SafeToPayPlansService();

            $plano = $safe2pay->getSupplierPlan($request->plan_id);
    
            return view('supplier.plans.selected', compact('plano'));
        
        }elseif ($admins->plano_f == 1){

            $plano = Fornecedorplano::where('id' , $request->plan_id)->first();
            $supplier_contact_plano = SupplierContractedPlans::where('supplier_id', $supplier->id)->first();

          
            if ($supplier_contact_plano){
            if ($supplier_contact_plano->name_plan == 'FREE'){
                $supplier_invoice = new supplier_invoice();
                $supplier_invoice->supplier_id = $supplier->id;
                $supplier_invoice->plan = $plano->descricao;
                $supplier_invoice->sub_total = $plano->valor; 
                $supplier_invoice->total = $plano->valor;
                $supplier_invoice->status = 'active';
                $supplier_invoice->payment = 'pending';
                $datefn = $supplier_contact_plano->due_date;
                if ($plano->cliclo = 'Mensal') {
                    $supplier_invoice->due_data = date("Y-m-d", strtotime($datefn.'+ 30 days')); 

                }elseif ($plano->cliclo = 'Trimestral') {
                    $supplier_invoice->due_data = date("Y-m-d", strtotime($datefn.'+ 90 days'));

                }elseif ($plano->cliclo = 'Semetral') {
                    $supplier_invoice->due_data = date("Y-m-d", strtotime($datefn.'+ 120 days'));

                }elseif ($plano->cliclo = 'Anual') {
                    $supplier_invoice->due_data = date("Y-m-d", strtotime($datefn.'+ 365 days'));

                }
                $supplier_invoice->due_date = $supplier_contact_plano->due_date;
                $supplier_invoice->save();


                 
            } else {

                $supplier_invoice = new supplier_invoice();
                $supplier_invoice->supplier_id = $supplier->id;
                $supplier_invoice->plan = $plano->descricao;
                $supplier_invoice->sub_total = $plano->valor; 
                $supplier_invoice->total = $plano->valor;
                $supplier_invoice->status = 'active';
                $supplier_invoice->payment = 'pending';
                $supplier_invoice->due_date = date('Y-m-d');
                $supplier_invoice->save();
            }
            $supplier_contact_plano->subscription_status = 'active';
            $datefn = date('Y-m-d');
                if ($plano->cliclo = 'Mensal') {
                    $supplier_contact_plano->due_date = date("Y-m-d", strtotime($datefn.'+ 30 days')); 

                }elseif ($plano->cliclo = 'Trimestral') {
                    $supplier_contact_plano->due_date = date("Y-m-d", strtotime($datefn.'+ 90 days'));

                }elseif ($plano->cliclo = 'Semetral') {
                    $supplier_contact_plano->due_date = date("Y-m-d", strtotime($datefn.'+ 120 days'));

                }elseif ($plano->cliclo = 'Anual') {
                    $supplier_contact_plano->due_date = date("Y-m-d", strtotime($datefn.'+ 365 days'));

                }
          //  $supplier_contact_plano->due_data = 
            $supplier_contact_plano->save();
            return redirect()->route('supplier.plans.invoice')->with('success', 'Plano Alterado com sucesso.');

           

        }else {
            
            $supplier_invoice = new supplier_invoice();
            $supplier_invoice->supplier_id = $supplier->id;
            $supplier_invoice->plan = $plano->descricao;
            $supplier_invoice->sub_total = $plano->valor; 
            $supplier_invoice->total = $plano->valor;
            $supplier_invoice->status = 'active';
            $supplier_invoice->payment = 'pending';
            $supplier_invoice->due_date = date('Y-m-d');
            $supplier_invoice->save();
        //dd($plano);
            $supplier_contact_plano = new SupplierContractedPlans();            
            $supplier_contact_plano->supplier_id = $supplier->id;        
            $supplier_contact_plano->name_plan = $plano->descricao;
            $supplier_contact_plano->plan_id =$plano->id;
            $supplier_contact_plano->valor = $plano->valor;
            $supplier_contact_plano->subscription_status = 'active';
       
       
        $datefn = date("Y-m-d");
        if ($plano->cliclo = 'Mensal') {
            $supplier_contact_plano->due_date = date("Y-m-d", strtotime($datefn.'+ 30 days')); 

        }elseif ($plano->cliclo = 'Trimestral') {
            $supplier_contact_plano->due_date = date("Y-m-d", strtotime($datefn.'+ 90 days'));

        }elseif ($plano->cliclo = 'Semetral') {
            $supplier_contact_plano->due_date = date("Y-m-d", strtotime($datefn.'+ 120 days'));

        }elseif ($plano->cliclo = 'Anual') {
            $supplier_contact_plano->due_date = date("Y-m-d", strtotime($datefn.'+ 365 days'));

        }
        

        $supplier_contact_plano->save();
        return redirect()->route('supplier.plans.invoice')->with('success', 'Plano Alterado com sucesso.');
   
        }
              

        }elseif ($admins->plano_f == 0){

                              
            return redirect()->route('supplier.plans.invoice')->with('success', 'Todos os Planos para Fornecedor e Gratuito .');

        }
   
    }
    
    public function store(Request $request){
        $supplier = Auth::guard('supplier')->user();

        //faz a requisição para cadastrar a nova subscrição no plano
        $safe2pay = new SafeToPayPlansService();
        $subscriptionResponse = $safe2pay->storeSupplierSubscription($request);
        if($subscriptionResponse && $subscriptionResponse['status'] == 'Ativa'){ //caso tenha retornado os dados corretamente
            //cria ou edita o plano do usuário
            $supplierContractedPlan = SupplierContractedPlans::firstOrCreate([
                'supplier_id' => $supplier->id
            ]);

            $supplierContractedPlan->plan_id = $request->plan_id;
            $supplierContractedPlan->subscription = $subscriptionResponse['subscription'];
            $supplierContractedPlan->transaction = $subscriptionResponse['transaction'];
            $supplierContractedPlan->subscription_status = $subscriptionResponse['status'];
        }
        
        if($supplierContractedPlan->save()){
            
            return redirect()->route('supplier.settings.index')->with('success', 'Plano atualizado com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Erro ao atualizar plano.');
        }
    }

    public function cancel(){
        return view('supplier.plans.cancel');
    }

    public function storeCancel(Request $request){
        $supplier = Auth::guard('supplier')->user();
        $safe2pay = new SafeToPayPlansService();
        
        //pega os dados do vencimento
        $planoSupplier = $safe2pay->getSupplierSubscription($supplier->contracted_plan->subscription); //pega os dados do plano do supplier atual, passa o id da Subscription
        $dataPagamento = $planoSupplier->SubscriptionCharges[0]->ChargeDate;
        $vencimentoPlano = date("d/m/Y", strtotime('+29 days', strtotime($dataPagamento))); //coloquei 29 dias só pq eu acho que no 30º dia ele vai cobrar novamente no cartão
        $vencimentoPlano = str_replace("/", "-", $vencimentoPlano);
        
        //cria a solicitação de cancelamento        
        $supplierCanceledPlan = SupplierCanceledPlans::firstOrCreate(['supplier_id' => $supplier->id]);
        
        $supplierCanceledPlan->plan_id = $supplier->contracted_plan->plan_id; //id do plano cancelado
        $supplierCanceledPlan->subscription = $supplier->contracted_plan->subscription; //assinatura que está sendo cancelada
        $supplierCanceledPlan->status = 'pending'; //status como pending, pq ela só muda para executed dps do cronjob fazer a mudança na data especificada
        
        $supplierCanceledPlan->change_date = date('Y-m-d', strtotime($vencimentoPlano)); //data em que o plano passará para gratuito        
        $supplierCanceledPlan->reason_cancellation = ($request->reason_cancellation ? $request->reason_cancellation : NULL);

        if($supplierCanceledPlan->save()){
            return redirect()->route('supplier.settings.index')->with('success', 'Plano cancelado com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Erro ao cancelar plano.');
        }
    }

    public function invoice(){

        $supplier = Auth::guard('supplier')->user();
        $supplier_invoice = SupplierContractedPlans::where('supplier_id' , $supplier->id)->orderBy('id', 'desc')->first();
        $supplier_invoice_all = Supplier_invoice::where('supplier_id' , $supplier->id)->orderBy('id', 'desc')->get();
        
      
        return view('supplier.plans.supplier_invoice' , compact('supplier_invoice', 'supplier_invoice_all'));
    }

    public function paymentpay($id)
    {
        $supplier = Auth::guard('supplier')->user();
        $supplier_invoice = Supplier_invoice::where('id' , $id)->orderBy('id', 'desc')->first();
        $admins = Admins::find(2);
      
        return view('supplier.plans.payment_plan' , compact('supplier_invoice' , 'admins' ));
        
    }

    public function planspay($id, Request $request){

        
        $admins = Admins::find(2);
        $supplier = Auth::guard('supplier')->user();
        $supplier_invoice = Supplier_invoice::where('id' , $id)->first();

       
       

        if (isset($admins->geren_cliente_id) and $admins->geren_cliente_id != null ){
        
            if ($supplier->legal_name === null){
               return redirect()->back()->with(['error' => 'Cadastro do Perfil  não esta completo erro no nome.']);
   
           }
           
           if ($supplier->document  === null){
               return redirect()->back()->with(['error' => 'Cadastro do Perfil  não esta completo erro cpf/cnpj.']);
   
           }
        }

        $metododepg = $request->payment_method;
        $gerencianet = new Gerencianetpay();      


        if($metododepg == 'pix'){
            $teste = $gerencianet->payplansupplierpix($metododepg , $supplier_invoice , $supplier ,$admins );
           
            return redirect()->back()->with([$teste['status'] => $teste['message']]);

            


        }elseif ($metododepg == 'boleto'){
            $teste = $gerencianet->payplansupplierpboleto($metododepg , $supplier_invoice , $supplier,$admins  );
         
         return redirect()->back()->with([$teste['status'] => $teste['message']]);


        }


    }

    public function pay_plano_consulta(Request $request){
       
        $id = $request->id;
        $supplier = Auth::guard('shop')->user();
        $plan_invoice = Supplier_invoice::where('id' , $request->id)->first();
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