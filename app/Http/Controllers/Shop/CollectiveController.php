<?php
namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Models\CollectiveImport;
use DataTables;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Services\CollectiveService;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
class CollectiveController extends Controller
{
    public function index(){

        return view('shop.collective.index');
    }

    public function new(){

        return view('shop.collective.new');
    }

    public function tabelas(Request $request)
    {
        $shop = Auth::user();

        if ($request->ajax()) {
            $data = CollectiveImport::select([
                    'collective_import.id', 
                    'collective_import.type_order', 
                    'collective_import.created_at', 
                    'collective_import.updated_at',
                    'collective_import.cost_price',
                    'collective_import.status', 
                    'collective_import.shop_id',
                    DB::raw("CASE WHEN collective_import.type_order = 1 THEN shops.responsible_name ELSE shops.fantasy_name END AS shop_name")
                ])
                ->join('shops', 'shops.id', '=', 'collective_import.shop_id')
                ->where('collective_import.shop_id', $shop->id)
                ->get();
                                        
            return Datatables::of($data)->addIndexColumn()
                ->editColumn('created_at', function($row){
                    return $row->created_at->format('Y-m-d');
                })
                ->editColumn('updated_at', function($row){
                    return $row->updated_at->format('Y-m-d');
                })
                ->editColumn('cost_price', function($row){
                    return $row->cost_price;
                })
                ->addColumn('status', function($row){
                    return $row->status;
                })->addColumn('action', function($row) {
                    $btnShow = '<a href="javascript:void(0)" onclick="show(' . $row->id . ')" name="show" id="show_' . $row->id . '" class="btn btn-primary btn-circle" role="button"><i class="fas fa-eye"></i></a>';
                
                    // Define the URL for the shopping cart icon.
                    $imageUrl = asset('assets/img/cart-shopping.svg');
                
                    // Check if the status is "PAGAMENTO PENDENTE" to add the buy button.
                    $btnBuy = '';
                    if ($row->status == "PAGAMENTO PENDENTE") {
                        $btnBuy = '<a href="javascript:void(0)" onclick="buy_stripe(' . $row->id . ')" name="buy_stripe" id="buy_stripe_' . $row->id . '" class="btn btn-secondary btn-circle" role="button"><img src="' . $imageUrl . '" height="15" width="15"/></a>';
                    }
                
                    // Concatenate the buttons.
                    $btn = $btnShow . $btnBuy;
                
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
                
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type_order' => 'required',
            'invoice_path' => 'required|file|mimes:pdf',
            'packing_list_path' => 'required|file|mimes:pdf',
            'produto_link' => 'required|url',
            'china_supplier_name' => 'required',
            'china_supplier_contact' => 'required',
            'product_description' => 'required',
            'product_hs_code' => 'required',
        ]);

        $invoicePath = $request->file('invoice_path')->store('invoices');
        $packingListPath = $request->file('packing_list_path')->store('packing_lists');

        $collectiveImport = new CollectiveImport();
        $collectiveImport->type_order = $request->type_order;
        $collectiveImport->invoice_path = $invoicePath;
        $collectiveImport->packing_list_path = $packingListPath;
        $collectiveImport->produto_link = $request->produto_link;
        $collectiveImport->china_supplier_name = $request->china_supplier_name;
        $collectiveImport->china_supplier_contact = $request->china_supplier_contact;
        $collectiveImport->product_hs_code = $request->product_hs_code;
        $collectiveImport->product_description = $request->product_description;
        $collectiveImport->status = 'EM ANALISE';
        $collectiveImport->shop_id = Auth::user()->id;
        $collectiveImport->save();

        return redirect()->route('shop.collective.index')->with('success', 'Pedido enviado com sucesso.');
    }

    public function show($collective_id)
    {
        $shop = Auth::user();
        $collectiveService = new CollectiveService($shop);
        $collective = $collectiveService->find($collective_id);
    
        if (!$collective) {
            return redirect()->route('collective.index')->withErrors('Importação coletiva não encontrada.');
        }
    
        return view('shop.collective.show', compact('collective'));
    }

    public function handleWebhook(Request $request)
    {
    $event = $request->all();

    if ($event['type'] == 'checkout.session.completed') {
        $session = $event['data']['object'];
        
        $collectiveImport = CollectiveImport::find($session['client_reference_id']);

        if ($collectiveImport) {
            $collectiveImport->status = 'PAGO';
            $collectiveImport->save();
            }
    }

    return response()->json(['status' => 'success']);
}

public function buy($collective_id)
{
    Stripe::setApiKey(env('STRIPE_SECRET'));

    $shop = Auth::user();
    $collectiveImport = CollectiveImport::where('id', $collective_id)->where('shop_id', $shop->id)->firstOrFail();

    // Calcula o custo total com a taxa da plataforma
    $costPrice = $collectiveImport->cost_price;
    $additionalFee = 1.00; // Taxa fixa
    $percentageFee = 0.0599; // 5,99%
    $totalFee = $additionalFee + ($costPrice * $percentageFee);
    $finalPrice = $costPrice + $totalFee;
    $finalPriceInCents = (int) round($finalPrice * 100); // Convertido para centavos

    try {
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => [
                        'name' => 'Importação Coletiva ID: '.$collective_id,
                    ],
                    'unit_amount' => $finalPriceInCents,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'phone_number_collection' => ['enabled' => true],
            'success_url' => route('shop.collective.success', ['collective_id' => $collective_id]), // Correct route name
            'cancel_url' => route('shop.collective.cancel', ['collective_id' => $collective_id]), // Correct route name
        ]);

        return redirect()->to($session->url);
    } catch (\Exception $e) {
        return redirect()->back()->withErrors('Erro ao criar a sessão de pagamento: '.$e->getMessage());
    }
}

    public function success(Request $request, $collective_id)
    {
        $collectiveImport = CollectiveImport::find($collective_id);
        if ($collectiveImport) {
            $collectiveImport->status = 'PAGO';
            $collectiveImport->save();
        }

        return redirect()->route('shop.collective.index')->with('success', 'Pagamento concluído com sucesso.');
    }

    public function cancel(Request $request, $collective_id)
    {
        return redirect()->route('shop.collective.index')->with('error', 'Pagamento foi cancelado.');
    }
}
