<?php

namespace App\Http\Controllers\Shop;

use App\Models\CartxApps;
use App\Models\PaymentInternalSubscriptionShop;
use App\Models\ShopifyApps;
use App\Models\WoocommerceApps;
use App\Models\YampiApps;
use App\Services\SafeToPayPlansService;
use Auth;
use Automattic\WooCommerce\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Tutorial;
use App\Models\Mercadolivreapi;
use App\Services\Shop\MercadolivreService;

class SettingsController extends Controller
{
    public function index()
    {
        $shop = Auth::guard('shop')->user();
        $mercaodlivreapi = Mercadolivreapi::where('shop_id' , $shop->id)->first();

     /*   $safe2pay = new SafeToPayPlansService();

        $planoShop = NULL;
        $vencimentoPlano = '';
        $stringPlanoGratuito = '';

        $dataVencimentoGratuito = date("Y-m-d", strtotime('+14 days', strtotime($shop->created_at))); //vencimento do plano gratuito
        $dataAtual = date("Y-m-d");

        if ($dataAtual <= $dataVencimentoGratuito) {
            $isFree = true;
        } else {
            $isFree = false;
        }

        if ($shop->contracted_plan && !$shop->internal_subscription) {
            $planoShop = $safe2pay->getShopSubscription($shop->contracted_plan ? $shop->contracted_plan->subscription : NULL); //pega os dados do plano do shop atual, passa o id da Subscription

            //tive que colocar esse código aqui também por conta do acesso de admin, essa atualização é feita no controller do login do shop,
            //ou seja, só é chamado quando o usuário fornece login e senha
            //faz a atualização do plano do usuário antes dele entrar
            $safe2pay->updateShopSubscription($shop);

            $diasVencimento = '';

            if ($planoShop) {
                if ($planoShop->Plan->PlanFrequence->Name == 'Anual') {
                    $diasVencimento = '365';
                }

                if ($planoShop->Plan->PlanFrequence->Name == 'Semestral') {
                    $diasVencimento = '182';
                }

                if ($planoShop->Plan->PlanFrequence->Name == 'Mensal') {
                    $diasVencimento = '30';
                }

                if ($planoShop && count($planoShop->SubscriptionCharges) > 0 && $planoShop->SubscriptionCharges[0]) {
                    $dataPagamento = $planoShop->SubscriptionCharges[0]->ChargeDate;
                    $vencimentoPlano = date("d/m/Y", strtotime('+' . $diasVencimento . ' days', strtotime($dataPagamento)));

                } else {
                    $dataPagamento = '';
                    $vencimentoPlano = '';
                }

                $isFree = false;
            }

            $dataVencimentoGratuito = date("Y-m-d", strtotime('+14 days', strtotime($shop->created_at))); //vencimento do plano gratuito
            $dataAtual = date("Y-m-d");

            //caso ainda esteja no periodo gratuito

            if (!$shop->contracted_plan && $dataAtual <= $dataVencimentoGratuito) {
                $stringPlanoGratuito = 'Você está no período gratuito que vence no dia ' . date("d/m/Y", strtotime($dataVencimentoGratuito)) . '. Aproveite a nossa plataforma!';
                $isFree = true;
            }
            if ((!$shop->contracted_plan || $shop->contracted_plan->subscription_status != 'Ativa') && $dataAtual > $dataVencimentoGratuito) {
                $stringPlanoGratuito = 'Seu período de testes da plataforma expirou! Por favor escolha outro plano para continuar utilizando nossos serviços.';
                $isFree = false;
            }
        }

        if (!$shop->contracted_plan && $shop->internal_subscription) {
            //carrega a ultima compra de produto desse usuário
            $lastPaymentInternalSubscription = PaymentInternalSubscriptionShop::where('internal_subscription_shop_id', $shop->internal_subscription->id)
                ->orderBy('id', 'desc')
                ->first();
            //caso esse objeto exista, calcula a nova data de vencimento
            $planDescription = '';
            $planValue = '';
            $planFrequence = '';

            if ($lastPaymentInternalSubscription) {
                if ($shop->internal_subscription->plan_id == 7739) {
                    $planDescription = "Plano " . config('app.name') . " para o Lojista Anual";
                    $diasVencimento = '365';
                    $planValue = '923,70';
                    $planFrequence = 'Anual';
                }

                if ($shop->internal_subscription->plan_id == 7736) {
                    $planDescription = "Plano " . config('app.name') . " para o Lojista Semestral";
                    $diasVencimento = '182';
                    $planValue = '539,40';
                    $planFrequence = 'Semestral';
                }
                $dataPagamento = $lastPaymentInternalSubscription->created_at;
                $vencimentoPlano = date("d/m/Y", strtotime('+' . $diasVencimento . ' days', strtotime($dataPagamento)));

                $isFree = false;
            }

            //caso ainda esteja no periodo gratuito
            $dataVencimentoGratuito = date("Y-m-d", strtotime('+14 days', strtotime($shop->created_at))); //vencimento do plano gratuito
            $dataAtual = date("Y-m-d");
            if (!$shop->internal_subscription && $dataAtual <= $dataVencimentoGratuito) {
                $stringPlanoGratuito = 'Você está no período gratuito que vence no dia ' . date("d/m/Y", strtotime($dataVencimentoGratuito)) . '. Aproveite a nossa plataforma!';
                $isFree = true;
            }
            if ((!$shop->internal_subscription || $shop->internal_subscription->status != 'active') && $dataAtual > $dataVencimentoGratuito) {
                $stringPlanoGratuito = 'Seu período de testes da plataforma expirou! Por favor escolha outro plano para continuar utilizando nossos serviços.';
                $isFree = false;
            }

            $arrStatus = ['active' => 'Ativa', 'inactive' => 'Inativa', 'overdue' => 'Atrasada', 'pending' => 'Pendente']; //status

            if ($shop->internal_subscription->status) {
                $statusString = $arrStatus[$shop->internal_subscription->status];
            } else {
                $statusString = 'Ativa';
            }

            $planoShop = (object)[
                'name' => $planDescription,
                'status' => $statusString,
                'value' => $planValue,
                'payment_method' => 'Cartão de Crédito',
                'frequence' => $planFrequence
            ];
        } */

        $tutorial = Tutorial::all();

      //dd($mercaodlivreapi);

        return view('shop.settings.index', compact('tutorial' , 'mercaodlivreapi'));
    }

    public function updateShopifyApp(Request $request)
    {
        $shop = Auth::guard('shop')->user();
        $shopify_app = ShopifyApps::firstOrCreate(['shop_id' => $shop->id]);

        $shopify_app->domain = $request->domain;
        $shopify_app->app_key = $request->app_key;
        $shopify_app->app_password = $request->app_password;
        $shopify_app->automatic_order_update = ($request->automatic_order_update == 'on') ? 1 : 0;
        $shopify_app->token = $request->token;
        $shopify_app->api_version = $request->api_version;

        try {
            $shopify_app->domain = preg_replace("/(https:\/\/|http:\/\/)/", "", $shopify_app->domain);
            $shopify_app->domain = rtrim($shopify_app->domain, "/");
            $shopify_app->domain = str_replace('.myshopify.com', '', $shopify_app->domain);
            $base_url = "https://{$shopify_app->domain}.myshopify.com";
            $headers = [
                'base_url' => $base_url,
                'request.options' => [
                    'headers' => [
                        'X-Shopify-Access-Token' => $shopify_app->token,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json; charset=utf-8;'
                    ]
                ]
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $base_url, [$headers]);
            $status_code = $response->getStatusCode();
        } catch (Exception $e) {
            $status_code = $e->getCode();
            report($e);
        }

        // Error statuses
        if ($status_code >= 400) {
            switch ($status_code) {
                case 404:
                    return redirect()->back()->with('error', 'Domínio inválido.');
                    break;
                case 403:
                    return redirect()->back()->with('error', 'Chave de acesso (APP KEY) inválida.');
                    break;
                case 401:
                    return redirect()->back()->with('error', 'Senha de acesso (APP PASSWORD) inválida.');
                    break;
                default:
                    return redirect()->back()->with('error', 'Erro ao atualizar os dados do app privado do Shopify, verifique-os e tente novamente em alguns minutos.');
                    break;
            }
        }

        if ($shopify_app->save()) {
            return redirect()->back()->with('success', 'Dados do APP privado do Shopify atualizados com sucesso.');
        } else {
            return redirect()->back()->with('error', 'Erro ao atualizar os dados do app privado do Shopify, tente novamente em alguns minutos.');
        }
    }

    public function updateWoocommerceApp(Request $request)
    {
        try {

            $shop = Auth::guard('shop')->user();

            $woocommerce_app = WoocommerceApps::firstOrCreate(['shop_id' => $shop->id]);

            $woocommerce_app->domain = $request->domain;
            $woocommerce_app->app_key = $request->app_key;
            $woocommerce_app->app_password = $request->app_password;
            $woocommerce_app->automatic_order_update = ($request->automatic_order_update == 'on') ? 1 : 0;

            //$client = new \GuzzleHttp\Client();
            // dd([ $request->app_key, $request->app_password ]);
            // dd($request->domain.'/wp-json/wc/v3/orders');

            $woocommerce = new Client(
                $request->domain,
                $request->app_key,
                $request->app_password,
                [
                    'wp_api' => true,
                    'version' => 'wc/v3'
                ]
            );

            if ($woocommerce->get('settings')) { //caso tenha acesso as settings, quer dizer q deu certo
                if ($woocommerce_app->save()) {
                    return redirect()->back()->with('success', 'Dados do APP privado do Woocommerce atualizados com sucesso.');
                }
            }

            // $status_code = $response->getStatusCode();

            // // Error statuses
            // if($status_code >= 400){
            //     switch ($status_code){
            //         case 404:
            //             return redirect()->back()->with('error', 'Domínio inválido.');
            //             break;
            //         case 403:
            //             return redirect()->back()->with('error', 'Chave de acesso (APP KEY) inválida.');
            //             break;
            //         case 401:
            //             return redirect()->back()->with('error', 'Senha de acesso (APP PASSWORD) inválida.');
            //             break;
            //         default:
            //             return redirect()->back()->with('error', 'Erro ao atualizar os dados do app privado do Woocommerce, verifique-os e tente novamente em alguns minutos.');
            //             break;
            //     }
            // }

            Log::error('Erro ao salvar app privado woocommerce ' . $shop->name);
            return redirect()->back()->with('error', 'Erro ao atualizar os dados do app privado do Woocommerce, tente novamente em alguns minutos.');

        } catch (Exception $e) {
            $status_code = $e->getCode();
            Log::error($e);
            report($e);

            return redirect()->back()->with('error', 'Erro ao atualizar os dados do app privado do Woocommerce, tente novamente em alguns minutos.');
        }


    }

    public function updateCartxApp(Request $request)
    {
        //atualiza os dados da loja no cartx
        $shop = Auth::guard('shop')->user();

        $cartx_app = CartxApps::firstOrCreate(['shop_id' => $shop->id]);

        if (!$request->domain) {
            return redirect()->back()->with('error', 'Nome de domínio inválido.');
        }

        if (!$request->token) {
            return redirect()->back()->with('error', 'Token inválido.');
        }

        $cartx_app->domain = $request->domain;
        $cartx_app->token = $request->token;

        //verifica junto a cartx se os dados são válidos antes de salvar

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://accounts.cartx.io/api/' . $cartx_app->domain, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $cartx_app->token,
                    'Accept' => 'application/json',
                ]
            ]);

            //caso o token seja inválido
            /*
                    Mesmo a pessoa não tendo conta, retorna status 200, por isso fiz essa verificação aqui
            */
            if ($response->getBody() == 'Token mismatch') {
                return redirect()->back()->with('error', 'Token inválido.');
            }

            //$status_code = $response->getStatusCode();
        } catch (Exception $e) {
            $status_code = $e->getCode();

            report($e);
        }

        if ($cartx_app->save()) {
            return redirect()->back()->with('success', 'Dados do APP privado do Cartx atualizados com sucesso.');
        } else {
            return redirect()->back()->with('error', 'Erro ao atualizar os dados do app privado do Cartx, tente novamente em alguns minutos.');
        }
    }

    public function updateYampiApp(Request $request)
    {

        $shop = Auth::guard('shop')->user();


        $yampi_app = YampiApps::firstOrCreate(['shop_id' => $shop->id]);


        $yampi_app->domain = $request->domain;

        $yampi_app->app_key = $request->app_key;

        $yampi_app->app_password = $request->app_password;

        //$yampi_app->automatic_order_update = ($request->automatic_order_update == 'on') ? 1 : 0;


        try {

            $client = new \GuzzleHttp\Client();

            $response = $client->request('GET', 'https://api.dooki.com.br/v2/' . $yampi_app->domain . '/catalog/products/',

                ['headers' => [

                    'User-Token' => $yampi_app->app_key,

                    'User-Secret-Key' => $yampi_app->app_password,

                    'Content-Type' => 'application/json',

                ]


                ]);

            $status_code = $response->getStatusCode();

        } catch (Exception $e) {
            $status_code = $e->getCode();
            report($e);
            Log::error('Erro ao Conectar Yampi shop: ' . $shop->name . ' - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao atualizar os integração com a Yampi - Cliente não encontrado.');
        }


        // Error statuses

        if ($status_code >= 400) {
            $errorMessage = '';
            switch ($status_code) {
                case 404:
                    $errorMessage = 'Domínio inválido.';
                    break;
                case 403:
                    $errorMessage = 'Chave de acesso (APP KEY) inválida.';
                    break;
                case 401:
                    $errorMessage = 'Senha de acesso (APP PASSWORD) inválida.';
                    break;
                default:
                    $errorMessage = 'Erro ao atualizar os dados do app privado do yampi, verifique-os e tente novamente em alguns minutos.';
                    break;
            }
            return redirect()->back()->with('error', 'Erro ao atualizar os integração com a Yampi, ' . $errorMessage);
        }


        if ($yampi_app->save()) {

            return redirect()->back()->with('success', 'Integração com a Yampi atualizada com sucesso.');

        } else {

            return redirect()->back()->with('error', 'Erro ao atualizar os integração com a Yampi, tente novamente em alguns minutos.');

        }

    }

    public function removeFreeCard()
    {
        $shop = Auth::guard('shop')->user();

        if ($shop->token_card && $shop->token_card->delete()) {
            return redirect()->back()->with('success', 'Cartão de crédito removido com sucesso.');
        }
        return redirect()->back()->with('error', 'Erro ao remover cartão de crédito.');
    }


    public function updateMercadolivreApp(Request $request)
    {
        $shop = Auth::guard('shop')->user();
       

        if (Mercadolivreapi::where('shop_id', $shop->id)->get()->count() == 0) {
        
            $mercadolivre = new  Mercadolivreapi();
            $mercadolivre->app_id = $request->app_id;
            $mercadolivre->shop_id = $shop->id;
            $mercadolivre->secret_id = $request->secret_id;
            $mercadolivre->tipo_anuncio = $request->tipo_anuncio;
            $mercadolivre->save();
        }else {
            $mercadolivre = Mercadolivreapi::where('shop_id' , $shop->id)->first();
            $mercadolivre->app_id = $request->app_id;
            $mercadolivre->secret_id = $request->secret_id;
            $mercadolivre->tipo_anuncio = $request->tipo_anuncio;
            $mercadolivre->save();
        }

        $apimercadolivre = Mercadolivreapi::where('shop_id' , $shop->id)->first();
        $tokenml = MercadolivreService::getToken($shop, $apimercadolivre );
       
        $mytime = date('Y-m-d H:i:s');
        if($tokenml){
        $apimercadolivre->token = $tokenml;
        $apimercadolivre->token_exp = date($mytime, strtotime('+4 Hours'));
        $apimercadolivre->save();

       // $usuarioml = MercadolivreService::getusuario($shop, $apimercadolivre );
        
        }
        
    //    dd($usuarioml);
      //  $mercadolivre->save();


        if($apimercadolivre->token <> null){
        return redirect()->back()->with('success', 'Api Mercadolivre atualizado com sucesso.');
    }else{
        return redirect()->back()->with('error', 'Erro ao Atualizar o cadastro, credenciais invalidas confirme suas credenciais.');
    } 
   
    return redirect()->back()->with('error', 'Erro ao Atualizar o cadastro, credenciais invalidas confirme suas credenciais.');
    
}

  

    public function updateShopeeApp(Request $request)
    {
        $shop = Auth::guard('shop')->user();
        return redirect()->back()->with('error', 'Aguarde Estamos atualizando a api shoppe.');

    }    

}
