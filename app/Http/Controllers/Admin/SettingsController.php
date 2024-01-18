<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use App\Models\AdminMelhorEnvioSettings;
use App\Models\Admins;
use App\Services\MelhorEnvioService;

class SettingsController extends Controller
{
    public function index(Request $request){
        //carrega os dados da primeira conta melhor
        $melhorEnvioSettings1 = AdminMelhorEnvioSettings::find(1);
        
        $admin = Auth::user();

        
        if($request->query('code') && $melhorEnvioSettings1){
            //dd($request->query('code'));
            //faz a requisição do token
            $melhorEnvioService = new MelhorEnvioService($melhorEnvioSettings1);
           
          
            if($melhorEnvioService->getToken($request->query('code'))){ //caso tenha dado certo a autorização
                return redirect()->route('admin.settings.index', ['success' => 'Autorização Melhor Envio salva com sucesso!']);
            }
            return redirect()->route('admin.settings.index', ['error' => 'Erro ao salvar autorização Melhor Envio.']);
        }

    	return view('admin.settings.index', compact('melhorEnvioSettings1' , 'admin' ));
    }

    public function updateMelhorEnvioSettings(Request $request){
        //oficial
        $linkApi = 'https://melhorenvio.com.br';
        $linkCallBack = 'https://app.dropshopmix.com/admin/settings'; //ou https://gruposhopmix.mawapost.com/supplier/settings

        //sandbox
        // $linkApi = 'https://sandbox.melhorenvio.com.br';
        // $linkCallBack = 'https://mawa-melhor-envio.herokuapp.com/admin/settings';


        $clientId = $request->client_id;
    
        $melhor_envio_settings = AdminMelhorEnvioSettings::firstOrNew([
            'client_id' => $request->client_id,
            'secret' => $request->secret
        ]);
        
        if($request->name){
            $melhor_envio_settings->name = $request->name;
            $melhor_envio_settings->save();
        }       

        //faz o redirect para o obter o code
        return redirect($linkApi.'/oauth/authorize?client_id='.$clientId.'&redirect_uri='.$linkCallBack.'&response_type=code&scope=cart-read cart-write companies-read companies-write coupons-read coupons-write notifications-read orders-read products-read products-write purchases-read shipping-calculate shipping-cancel shipping-checkout shipping-companies shipping-generate shipping-preview shipping-print shipping-share shipping-tracking ecommerce-shipping transactions-read users-read users-write');
    }

    public function updateSettings(Request $request){
   
        $admin = Auth::user();

        $admins = new Admins();

        $admins->plano_f = $request->plano_f;
        $admins->plano_shop = $request->plano_shop;
        $admins->free_shop = $request->free_shop;
        $admins->cad_supplier = $request->cad_supplier;
        $admins->cad_shop = $request->cad_shop;
        $admins->catalogo = $request->catalogo;
        $admins->pg_pix = $request->pg_pix;
        $admins->pg_boleto = $request->pg_boleto;
        $admins->pg_cartao = $request->pg_cartao;
        $admins->bloq_acesso = $request->bloq_acesso;
        $admins->price_catalog = $request->price_catalog; 
        $admins->taxapix = $request->taxapix; 	
        

         

        if($admins->save()){
            return redirect()->route('admin.settings.index')->with('success', 'Configurações atualizada com sucesso.');
        }else{
            return redirect()->route('admin.settings.index')->with('error', 'Erro ao atualizar. Tente novamente em alguns minutos.');
        }


        

    }    
    

}
