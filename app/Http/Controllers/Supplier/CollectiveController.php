<?php
namespace App\Http\Controllers\Supplier;

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Models\CollectiveImport;
use Auth;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Services\CollectiveService;

class CollectiveController extends Controller
{
    public function index(){

        return view('supplier.collective.index');
    }

    public function edit($collective_id)
    {
        $supplier = Auth::user();
        $collective = CollectiveImport::with('shop.address')->where('id', $collective_id)->first();
    
        if (!$collective) {
            return redirect()->route('collective.index')->withErrors('Importação coletiva não encontrada.');
        }
    
        return view('supplier.collective.edit', compact('collective'));
    }
    
    public function tabelas(Request $request)
    {
        $supplier = Auth::user();

        if ($request->ajax()) {
            $data = CollectiveImport::select([
                    'collective_import.id', 
                    'collective_import.type_order', 
                    'collective_import.created_at', 
                    'collective_import.updated_at', 
                    'collective_import.status', 
                    'collective_import.shop_id',
                    DB::raw("CASE WHEN collective_import.type_order = 1 THEN shops.responsible_name ELSE shops.fantasy_name END AS shop_name")
                ])
                ->join('shops', 'shops.id', '=', 'collective_import.shop_id')
                ->where('collective_import.shop_id', $supplier->id)
                ->get();
                                        
            return Datatables::of($data)->addIndexColumn()
                ->editColumn('created_at', function($row){
                    return $row->created_at->format('Y-m-d');
                })
                ->editColumn('updated_at', function($row){
                    return $row->updated_at->format('Y-m-d');
                })
                ->addColumn('shop_name', function($row){
                    // Já estamos buscando o shop_name diretamente na consulta, então apenas retornamos
                    return $row->shop_name;
                })
                ->addColumn('status', function($row){
                    return $row->status;
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" onclick="show('.$row->id.')" name="show" id="'.$row->id.'" class="btn btn-primary btn-circle" role="button"><i class="fas fa-eye"></i></a>';
                    $btn .= '<a href="javascript:void(0)" onclick="edit('.$row->id.')" name="edit" id="'.$row->id.'" class="btn btn-info btn-circle" role="button"><i class="fas fa-pencil-alt"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

  return view('supplier.products.index');
}
    public function update(Request $request, $collective_id)
    {
        $supplier = Auth::user();

        $collectiveService = new CollectiveService($supplier);
        try {
            $collectiveService->update($collective_id, $request);
            return redirect()->route('supplier.collective.index')->with('success', 'Importação coletiva atualizada com sucesso.');
        } catch (CustomException $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
    }


}
