<?php
namespace App\Http\Controllers\Supplier;

use Illuminate\Http\Request;

use App\Models\ShopRadar;
use DataTables;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Services\RadarService;


class RadarController extends Controller
{
    public function index()
        {
            return view('supplier.radar.index');
        }
        public function tabelas(Request $request)
        {
            $supplier = Auth::user();
    
            if ($request->ajax()) {
                $data = ShopRadar::select([
                        'shop_radar.id', 
                        'shop_radar.created_at', 
                        'shop_radar.updated_at', 
                        'shop_radar.status', 
                        'shop_radar.shop_id',
                        'shops.responsible_name'
                    ])
                    ->join('shops', 'shops.id', '=', 'shop_radar.shop_id')
                    ->where('shop_radar.shop_id', $supplier->id)
                    ->get();
                                            
                return Datatables::of($data)->addIndexColumn()
                    ->editColumn('created_at', function($row){
                        return $row->created_at->format('Y-m-d');
                    })
                    ->editColumn('updated_at', function($row){
                        return $row->updated_at->format('Y-m-d');
                    })
                    ->addColumn('responsible_name', function($row){
                        return $row->responsible_name;
                    })
                    ->addColumn('status', function($row){
                        return $row->status;
                    })
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" onclick="edit('.$row->id.')" name="edit" id="'.$row->id.'" class="btn btn-info btn-circle" role="button"><i class="fas fa-pencil-alt"></i></a>';
                        
                        return $btn;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
    
            return view('supplier.radar.index');
        }

        public function edit($radar_id)
        {
            $supplier = Auth::user();
            $radarService = new RadarService($supplier);
            $radar = $radarService->find($radar_id);
        
            if (!$radar) {
                return redirect()->route('radar.index')->withErrors('Importação coletiva não encontrada.');
            }
        
            return view('supplier.radar.edit', compact('radar'));
        }
}