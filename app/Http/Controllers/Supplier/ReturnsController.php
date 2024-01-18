<?php

namespace App\Http\Controllers\Supplier;

use App\Models\ReturnMessages;
use App\Models\Returns;
use App\Models\SupplierOrders;
use App\Services\ReturnsService;
use Illuminate\Http\Request;

class ReturnsController extends Controller{
    public function index(Request $request){
        $status = $request->status ? $request->status : 'pending';
        $supplier_id = auth()->guard('supplier')->id();

        $returns = Returns::whereHas('supplier_order', function($q) use ($supplier_id){
            $q->where('supplier_id', $supplier_id);
        })->where('status', $status)->get();

        return view('supplier.returns.index', compact('returns'));
    }

    public function show($id){
        $supplier_id = auth()->guard('supplier')->id();

        $return = Returns::whereHas('supplier_order', function($q) use ($supplier_id){
            $q->where('supplier_id', $supplier_id);
        })->find($id);

        if(!$return){
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }

        // Read pending messages
        ReturnMessages::where('return_id', $return->id)->whereNull('supplier_id')->update(['read' => 1]);

        return view('supplier.returns.show', compact('return'));
    }

    public function confirm($id){
        $supplier_id = auth()->guard('supplier')->id();

        $result = ReturnsService::confirmBySupplier($id, $supplier_id);

        return redirect()->back()->with(($result->success) ? 'success' : 'error', $result->message);
    }

    public function newMessage(Request $request, $id){
        $supplier_id = auth()->guard('supplier')->id();

        $return = Returns::whereHas('supplier_order', function($q) use ($supplier_id){
            $q->where('supplier_id', $supplier_id);
        })->find($id);

        if($return){
            $message = new ReturnMessages();

            $message->return_id = $return->id;
            $message->supplier_id = $supplier_id;
            $message->message = $request->message;

            $message->save();

            return redirect()->back()->with(['success' => 'Mensagem enviada com sucesso.']);
        }else{
            return redirect()->back()->with(['error' => 'Pedido não encontrado.']);
        }
    }
}
