<?php

namespace App\Http\Controllers\Supplier;

use App\Models\CorreiosContracts;
use App\Models\CorreiosSettings;
use App\Models\Discounts;
use App\Models\Products;
use App\Models\ProductVariants;
use App\Models\SupplierGateways;
use App\Models\TotalExpressSettings;
use App\Models\MelhorEnvioSettings;
use Illuminate\Http\Request;

use App\Models\MercadoPagoAccounts;
use App\Services\MelhorEnvioService;

use App\Services\SafeToPayPlansService;
use App\Services\SupplierPlanService;

use App\Models\SupplierCanceledPlans;
use App\Models\SupplierOrders;
use App\Models\SupplierBillingMonthly;

use Auth;

class SettingsController extends Controller
{
    public function index(Request $request){
        $supplier = Auth::user();

        $variants = ProductVariants::whereHas('product', function($q) use ($supplier){
            $q->where('supplier_id', $supplier->id);
        })->get();

        $contract = CorreiosContracts::where('supplier_id', $supplier->id)->first();

        if($request->query('code') && $supplier->melhor_envio_settings){
            //dd($request->query('code'));
            //faz a requisição do token
            $melhorEnvioService = new MelhorEnvioService($supplier->melhor_envio_settings);
            if($melhorEnvioService->getToken($request->query('code'))){ //caso tenha dado certo a autorização
                return redirect()->route('supplier.settings.index', ['success' => 'Autorização Melhor Envio salva com sucesso!']);
            }
            return redirect()->route('supplier.settings.index', ['error' => 'Erro ao salvar autorização Melhor Envio.']);
        }

        $safe2pay = new SafeToPayPlansService();
        
        $planoSupplier = $safe2pay->getSupplierSubscription($supplier->contracted_plan ? $supplier->contracted_plan->subscription : NULL); //pega os dados do plano do supplier atual, passa o id da Subscription
        //tive que colocar esse código aqui também por conta do acesso de admin, essa atualização é feita no controller do login do supplier,
        //ou seja, só é chamado quando o usuário fornece login e senha
        //faz a atualização do plano do usuário antes dele entrar
        $safe2pay->updateSupplierSubscription($supplier);
        
        $vencimentoPlano = '';
        if($planoSupplier){
            $dataPagamento = $planoSupplier->SubscriptionCharges[0]->ChargeDate;
            $vencimentoPlano = date("d/m/Y", strtotime('+30 days', strtotime($dataPagamento)));
        }

        //cronjob cancelamento
        //seleciona todos os pedidos de cancelamento para o dia que ainda não tenham sido executados
        // $suppliersCanceledPlans = SupplierCanceledPlans::where('change_date', date("Y-m-d"))
        //                                     ->where('status', 'pending')
        //                                     ->orderBy('id', 'asc')
        //                                     ->limit(100)
        //                                     ->get();
        
        // foreach ($suppliersCanceledPlans as $supplierCanceledPlan) {
        //     $supplier = Suppliers::find($supplierCanceledPlan->supplier_id);

        //     //cancela a assinatura na safe2pay
        //     $safe2pay->cancelSupplierSubscription($supplier);

        //     //muda o plano do usuário para o id do plano gratuito
        //     $supplier->contracted_plan->plan_id = 999; //magic number do plano gratuito
            
        //     if($supplier->contracted_plan->save()){
        //         //atualiza o supplier canceled para executed
        //         $supplier->canceled_plan->status = 'executed';
        //         $supplier->canceled_plan->save();
        //     }
        // }
        $supplierPlanService = new SupplierPlanService($supplier);
        $monthBilling = $supplierPlanService->getActualBilling();

        //caso ainda esteja no plano gratuito
        $stringPlanoGratuito = '';
        if(!$supplier->contracted_plan && $monthBilling <= 999.90){
            $stringPlanoGratuito = 'Você está dentro do plano gratuito. Aproveite a nossa plataforma!';
        }

        $monthBilling = number_format($supplierPlanService->getActualBilling(), 2, ',', '.');
        $nexPlan = number_format($supplierPlanService->getActualPlanValue(), 2, ',', '.');

    	return view('supplier.settings.index', compact('variants', 'contract', 'planoSupplier', 'vencimentoPlano', 'stringPlanoGratuito', 'monthBilling', 'nexPlan' , 'supplier'));
    }

    public function updateMPCredentials(Request $request){
    	$supplier = Auth::user();

    	$mp_account = MercadoPagoAccounts::firstOrCreate(['supplier_id' => $supplier->id]);
    	SupplierGateways::firstOrCreate(['gateway_id' => 2, 'supplier_id' => $supplier->id]);

    	$mp_account->public_key = $request->public_key;
    	$mp_account->access_token = $request->access_token;

    	if($mp_account->save()){
    		return redirect()->back()->with('success', 'Credenciais do mercado pago atualizadas com sucesso.');
    	}else{
    		return redirect()->back()->with('error', 'Aconteceu um erro inesperado ao atualizar suas credenciais do MercadoPago. Tente novamente em alguns minutos.');
    	}
    }

    public function updateBlingSettings(Request $request){
        $supplier = Auth::user();
        $supplier->bling_apikey = $request->bling_apikey;

        if($request->bling_automatic_tracking_code){
            $supplier->bling_automatic_tracking_code = $request->bling_automatic_tracking_code;
        }

        if($request->radio_stock){
            $supplier->empty_stock_bling = $request->radio_stock; //flag que controla se aceita ou não pedidos quando o bling informa que o estoque está vazio
        }
        
        if($supplier->save()){
            return redirect()->back()->with('success', 'Configurações da API do Bling atualizadas com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado ao atualizar a API do Bling. Tente novamente em alguns minutos.');
        }
    }

    public function updateChinaDivisionSettings(Request $request){
        $supplier = Auth::user();

        $supplier->china_division_apikey = $request->china_division_apikey;        
        
        if($supplier->save()){
            return redirect()->back()->with('success', 'Configurações da API do China Division atualizadas com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado ao atualizar a API do China Division. Tente novamente em alguns minutos.');
        }
    }

    public function updateCorreiosSettings(Request $request){
        $supplier = Auth::user();

        $correios_settings = CorreiosSettings::firstOrNew(['supplier_id' => $supplier->id]);

        $correios_settings->percentage = $request->percentage;
        $correios_settings->correios_services_bling = $request->correios_services_bling; //tipos de serviços de entrega dos correios oferecidos pelo bling

        if($correios_settings->save()){
            $supplier->shipping_method = 'correios';
            $supplier->save();

            return redirect()->back()->with('success', 'Configurações dos correios atualizadas com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado ao atualizar a porcentagem dos correios. Tente novamente em alguns minutos.');
        }
    }

    public function updateTotalExpressSettings(Request $request){
        $supplier = Auth::user();

        $correios_settings = TotalExpressSettings::firstOrNew(['supplier_id' => $supplier->id]);

        $correios_settings->type = $request->type;
        $correios_settings->login = $request->login;
        $correios_settings->password = $request->password;

        if(!$this->testCredentials($correios_settings)){
            return redirect()->back()->with('error', 'Dados da Total Express inválidos, verifique os dados digitados e tente novamente.');
        }

        if($correios_settings->save()){
            $supplier->shipping_method = 'total_express';
            $supplier->save();

            return redirect()->back()->with('success', 'Configurações de frete atualizadas com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado ao atualizar o frete. Tente novamente em alguns minutos.');
        }
    }

    public function updateMelhorEnvioSettings(Request $request){
        $supplier = Auth::user();

        $supplier->shipping_method = 'melhor_envio';
        
        if($supplier->save()){            
            return redirect()->back()->with('success', 'Configurações de frete atualizadas com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado ao atualizar o frete. Tente novamente em alguns minutos.');
        }
        

        // $melhor_envio_settings = MelhorEnvioSettings::firstOrNew(['supplier_id' => $supplier->id]);

        // // $melhor_envio_settings->client_id = $request->client_id;
        // // $melhor_envio_settings->secret = $request->secret;

        // // if(!$this->testCredentials($melhor_envio_settings)){
        // //     return redirect()->back()->with('error', 'Dados da Total Express inválidos, verifique os dados digitados e tente novamente.');
        // // } 

        // if($melhor_envio_settings->save()){
        //     $supplier->shipping_method = 'melhor_envio';
        //     $supplier->save();

        //     //faz um redirect pedindo a autorização do app
        //     $melhorEnvioService = new MelhorEnvioService($melhor_envio_settings);
        //     return $melhorEnvioService->getAuth();
        //     //return redirect()->back()->with('success', 'Configurações de frete atualizadas com sucesso.');
        // }else{
        //     return redirect()->back()->with('error', 'Aconteceu um erro inesperado ao atualizar o frete. Tente novamente em alguns minutos.');
        // }
    }

    public function removeMelhorEnvioSettings(){
        //remove as configurações da melhor envio
        $supplier = Auth::user();
        
        //volta o fornecedor para o frete gratuito
        $supplier->shipping_method = 'no_shipping';

        if($supplier->melhor_envio_settings->delete() && $supplier->save()){
            return redirect()->back()->with('success', 'Aplicativo Melhor Envio removido com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Erro ao remover aplicativo da Melhor Envio, tente novamente mais tarde.');
        }      
    }

    function testCredentials($settings){
        try {
            $client = new \SoapClient('https://edi.totalexpress.com.br/webservice_calculo_frete.php?wsdl', ['trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE, 'login' => $settings->login, 'password' => $settings->password]);

            $arguments= array('calcularFrete' => array(
                'TipoServico' => $settings->type,
                'CepDestino' => 95555000,
                'Peso' => 1,
                'ValorDeclarado' => 10,
                'TipoEntrega' => 0,
                'ServicoCOD' => false,
                'Altura' => 10,
                'Largura' => 10,
                'Profundidade' => 10
            ));

            $options = array('location' => 'https://edi.totalexpress.com.br/webservice_calculo_frete.php');

            $result = $client->__soapCall('calcularFrete', $arguments, $options);

            if($result->DadosFrete){
                return true;
            }else{
                return false;
            }
        } catch(\Exception $e){
            return false;

            report($e);
        }
    }

    public function updateNoShippingSettings(Request $request){
        $supplier = Auth::user();

        $supplier->shipping_method = 'no_shipping';

        if($supplier->save()){
            return redirect()->back()->with('success', 'Configurações de frete atualizadas com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado ao atualizar o frete. Tente novamente em alguns minutos.');
        }
    }

    public function updateShippingFee(Request $request){
        $supplier = Auth::user();

        $supplier->shipping_fixed_fee = $request->shipping_fixed_fee;

        if($supplier->save()){
            return redirect()->back()->with('success', 'Taxa de manuseio atualizada com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado ao atualizar a taxa de manuseio sobre o frete. Tente novamente em alguns minutos.');
        }
    }

    public function discounts(){
        $supplier = Auth::user();
        $discounts = Discounts::where('supplier_id', $supplier->id)->get();
        $variants = ProductVariants::whereHas('product', function($q) use ($supplier){
            $q->where('supplier_id', $supplier->id);
        })->get();
        return view('supplier.settings.discounts', compact('discounts', 'variants'));
    }

    public function store_discount(Request $request){
        $supplier = Auth::user();
        if($supplier){
            Discounts::create([
                'supplier_id' => $supplier->id,
                'variant_id' => $request->variant_id,
                'code' => $request->code,
                'percentage' => $request->percentage
            ]);

            return redirect()->back()->with('success', 'Desconto cadastrado com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Erro ao tentar cadastrar desconto, tente novamente.');
        }
    }

    public function delete_discount($id){
        $supplier = Auth::user();
        $discount = Discounts::where('supplier_id', $supplier->id)->where('id', $id)->first();

        if($discount){
            $discount->delete();

            return redirect()->back()->with('success', 'Desconto deletado com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Erro ao tentar deletar desconto, tente novamente.');
        }
    }

    public function updateCorreiosContract(Request $request){
        set_time_limit(30);

        $sigep_user = $request->sigep_user;
        $sigep_password = $request->sigep_password;
        $active = $request->active;
        $service_code = $request->service_code;

        $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa='.$sigep_user.'&sDsSenha='.$sigep_password.'&sCepOrigem=95555000&sCepDestino=95888000&nVlPeso=0.2&nCdFormato=1&nVlComprimento=24&nVlAltura=2&nVlLargura=16&sCdMaoPropria=N&nVlValorDeclarado=0&sCdAvisoRecebimento=N&nCdServico='.$service_code.'&nVlDiametro=0&StrRetorno=xml';;
        $response = simplexml_load_string(file_get_contents($url));
        $response_array = ['response' => $response, 'error_code' => (string)$response->cServico->Erro, 'error_message' =>(string)$response->cServico->MsgErro];

        if($response_array['error_code'] == 0){
            $supplier = Auth::user();

            if(!($sigep_user && $sigep_password && $service_code) && $active == 1){
                return redirect()->back()->with(['error' => 'Preencha todos os campos.']);
            }

            $contract = CorreiosContracts::where('supplier_id', $supplier->id)->first();

            if(!$contract) {
                $contract = new CorreiosContracts();
                $contract->supplier_id = $supplier->id;
            }

            $contract->sigep_user = $sigep_user;
            $contract->sigep_password = $sigep_password;
            $contract->active = $active;
            $contract->service_code = $service_code;

            $contract->save();

            return redirect()->back()->with(['success' => 'Configurações do contrato com correios configuradas com sucesso.']);
        }else{
            return redirect()->back()->with(['error' => $response_array['error_message']])->withInput();
        }

//        try {
//            $supplier = Auth::user();
//
//            $sigep_user = $request->sigep_user;
//            $sigep_password = $request->sigep_password;
//            $contract_id = $request->contract_id;
//            $post_card_id = $request->post_card_id;
//            $active = $request->active;
//            $administrative_code = $request->administrative_code;
//            $service_code = $request->service_code;
//
//            if(!($sigep_user && $sigep_password && $contract_id && $post_card_id) && $active == 1){
//                return redirect()->back()->with(['error' => 'Preencha todos os campos necessários.']);
//            }
//
//            $contract = CorreiosContracts::where('supplier_id', $supplier->id)->first();
//
//            if(!$contract) {
//                $contract = new CorreiosContracts();
//                $contract->supplier_id = $supplier->id;
//            }
//
//            $contract->sigep_user = $sigep_user;
//            $contract->sigep_password = $sigep_password;
//            $contract->contract_id = $contract_id;
//            $contract->post_card_id = $post_card_id;
//            $contract->administrative_code = $administrative_code;
//            $contract->active = $active;
//
//
//            $connectionArrray = [
//                'trace' => 1,
////                'cache_wsdl' => WSDL_CACHE_NONE,
//                'exceptions' => 1,
//                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
//                'connection_timeout' => 1000
//            ];
//            try {
//                $client = new \SoapClient('https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl', $connectionArrray);
//
//                $arguments = [
//                    'buscaCliente' => [
//                        'usuario' => $sigep_user,
//                        'senha' => $sigep_password,
//                        'idContrato' => $contract_id,
//                        'idCartaoPostagem' => $post_card_id,
//                    ]
//                ];
//
//                $result = $client->__soapCall('buscaCliente', $arguments);
//
//            }catch(\Exception $e){
//                return redirect()->back()->with(['error' => 'Erro na autenticação dos dados digitados, verifique os dados e tente novamente.'])->withInput();
//            }
//
//            if (isset($result->return->contratos->cartoesPostagem)) {
//                $servicesArray = [];
//                foreach($result->return->contratos->cartoesPostagem->servicos as $service){
//                    $servicesArray[] = ['code' => str_replace(' ', '', $service->codigo), 'name' => str_replace(' ', '', $service->descricao)];
//                }
//                if($service_code){
//                    $contract->service_code = $service_code;
//                }else{
//                    if(count($servicesArray) == 1){
//                        $contract->service_code = $servicesArray[0]['code'];
//                    }
//                }
//                $contract->administrative_code = $result->return->contratos->cartoesPostagem->codigoAdministrativo;
//                $contract->services = json_encode($servicesArray);
//
//                if($contract->post_card_id){
//                    $arguments_post_card = [
//                        'getStatusCartaoPostagem' => [
//                            'numeroCartaoPostagem' => $contract->post_card_id,
//                            'usuario' => $sigep_user,
//                            'senha' => $sigep_password
//                        ]
//                    ];
//                    try {
//                        $result_post_card = $client->__soapCall('getStatusCartaoPostagem', $arguments_post_card);
//                    }catch(\Exception $e){
//                        return redirect()->back()->with(['error' => 'Erro ao buscar cartão de postagem, verifique os dados e tente novamente.'])->withInput();
//                    }
//
//                    if(isset($result_post_card->return) && $result_post_card->return == 'Normal'){
//                        $contract->post_card_last_check = date('Y-m-d H:i:s');
//                    }
//                }
//
//                $contract->save();
//
//                if($contract->service_code != null){
//                    return redirect()->back()->with(['warning' => 'Configurações atualizadas com sucesso, verifique se todos os indicadores estão verdes (prontos para operar).']);
//                }else{
//                    return redirect()->back()->with(['warning' => 'Atenção, agora selecione o código do serviço para finalizar a configuração.']);
//                }
//            }else{
//                return redirect()->back()->with(['error' => 'Não foi possível consultar a API dos correios, verifique os dados e tente novamente.'])->withInput();
//            }
//        }catch (\Exception $e){
//            return redirect()->back()->with(['error' => $e->getMessage()])->withInput();
//        }
    }

    public function updateEtiquetaML(Request $request){
        //remove as configurações da melhor envio
        $supplier = Auth::user();
        
        
        $supplier->imp_etq_ml = $request->imp_etq_ml;

        if($supplier->update()){
            return redirect()->back()->with('success', 'Etiqueta configurada com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Erro ao configurar etiqueta, tente novamente mais tarde.');
        }      
    }
}
