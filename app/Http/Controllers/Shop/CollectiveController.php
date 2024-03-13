<?php
namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Models\CollectiveImport;

use Auth;

class CollectiveController extends Controller
{
    public function index(){

        return view('shop.collective.index');
    }

    public function new(){

        return view('shop.collective.new');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type_order' => 'required',
            'invoice_path' => 'required|file|mimes:pdf',
            'packing_list_path' => 'required|file|mimes:pdf',
            'produto_link' => 'required|url',
        ]);

        $invoicePath = $request->file('invoice_path')->store('invoices');
        $packingListPath = $request->file('packing_list_path')->store('packing_lists');

        $collectiveImport = new CollectiveImport();
        $collectiveImport->type_order = $request->type_order;
        $collectiveImport->invoice_path = $invoicePath;
        $collectiveImport->packing_list_path = $packingListPath;
        $collectiveImport->produto_link = $request->produto_link;
        $collectiveImport->status = 'EM ANALISE';
        $collectiveImport->shop_id = Auth::user()->id;
        $collectiveImport->save();

        return redirect()->route('shop.collective.index')->with('success', 'Pedido enviado com sucesso.');
    }
}
