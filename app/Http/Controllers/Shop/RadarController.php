<?php
namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Models\ShopRadar;

use Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Storage;

class RadarController extends Controller
{
    public function index(){
            $shopId = Auth::id();
            $shopRadar = ShopRadar::where('shop_id', $shopId)->first();
            
            return view('shop.radar.index', compact('shopRadar'));
        }

    public function buy(){

        return view('shop.radar.buy');
    }
    public function activate(){

        return view('shop.radar.activate');
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'radar_qualification' => 'required|file|mimes:pdf',
        ]);

        $radar = new ShopRadar();
        if ($request->hasFile('radar_qualification')) {
            $radarQuallificationPath = $request->file('radar_qualification')->store('radar_documents', 'public');
            $radar->radar_qualification = $radarQuallificationPath;
        }
        $radar->status = 'EM ANALISE';
        $radar->whatsapp = 0;
        $radar->shop_id = Auth::user()->id;
        $radar->save();

        return redirect()->route('shop.radar.index')->with('success', 'Radar enviado com sucesso.');
    }

    public function update_buy(Request $request)
    {
        $validatedData = $request->validate([
            'document' => 'required|file|mimes:pdf,png,jpg,jpeg',
            'social_contract' => 'required|file|mimes:pdf,png,jpg,jpeg',
            'cnpj' => 'required|file|mimes:pdf,png,jpg,jpeg',
            'bank_extract' => 'required|file|mimes:pdf,png,jpg,jpeg',
            'address_contract' => 'required|file|mimes:pdf,png,jpg,jpeg',
            'whatsapp' => 'required',
        ]);

        $radar = new ShopRadar();
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('radar_documents', 'public');
            $radar->document = $documentPath;
        }
        if ($request->hasFile('social_contract')) {
            $socialContractPath = $request->file('social_contract')->store('radar_documents', 'public');
            $radar->social_contract = $socialContractPath;
        }
        if ($request->hasFile('cnpj')) {
            $cnpjPath = $request->file('cnpj')->store('radar_documents', 'public');
            $radar->cnpj = $cnpjPath;
        }
        if ($request->hasFile('bank_extract')) {
            $bankExtractPath = $request->file('bank_extract')->store('radar_documents', 'public');
            $radar->bank_extract = $bankExtractPath;
        }
        if ($request->hasFile('address_contract')) {
            $addressContractPath = $request->file('address_contract')->store('radar_documents', 'public');
            $radar->address_contract = $addressContractPath;
        }
        $radar->whatsapp = 0;
        $radar->status = 'EM ANALISE';
        $radar->shop_id = Auth::user()->id;
        $radar->save();
        $price = 14790;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => 'Compra de Radar',
                        ],
                        'unit_amount' => $price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('shop.radar.success', ['radar_id' => $radar->id]),
                'cancel_url' => route('shop.radar.cancel', ['radar_id' => $radar->id]),
            ]);

            return redirect()->to($session->url);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Erro ao criar a sessão de pagamento: '.$e->getMessage());
        }
    }

    public function success(Request $request, $radar_id)
    {
        $radar = ShopRadar::find($radar_id);
    
        if ($radar) {
            $radar->status = 'PAGO';
            $radar->save();
        }
    
        return redirect()->route('shop.radar.index')->with('success', 'Pagamento concluído com sucesso. Em breve entrararemos em contato.');
    }
    

    public function cancel(Request $request, $radar_id)
    {
        $radar = ShopRadar::find($radar_id);
    
        if ($radar) {
            $radar->delete();
        }
    
        return redirect()->route('shop.radar.index')->with('error', 'Pagamento foi cancelado.');
    }
    
}