<?php

namespace App\Http\Controllers\Admin;

use App\Services\SafeToPayService;
use Illuminate\Http\Request;

use App\Models\Suppliers;
use App\Models\SupplierOrders;
use App\Models\Admins;

use Auth;

class SuppliersController extends Controller
{
    public function index(){
    	$suppliers = Suppliers::all();
        $supplierOrdersPending = SupplierOrders::where("status", "pending")->sum("amount");
        $supplierOrdersPaid = SupplierOrders::where("status", "paid")->sum("amount");
    	return view('admin.suppliers.index', compact('suppliers', 'supplierOrdersPending', 'supplierOrdersPaid'));
    }

    public function show(Suppliers $supplier){
        $admins = Admins::find(2);

    	return view('admin.suppliers.show', compact('supplier' , 'admins' ));
    }

    public function login(Suppliers $supplier){
    	Auth::guard('supplier')->login($supplier);

    	return redirect()->route('supplier.dashboard');
    }

    public function toggleStatus(Suppliers $supplier){
        $supplier->status = ($supplier->status == 'active') ? 'inactive' : 'active';
        $supplier->save();

        return redirect()->back()->with('success', ($supplier->status == 'active') ? 'Atividades do fornecedor reiniciadas com sucesso.' : 'Atividades do fornecedor pausadas com sucesso.');
    }

    public function toggleLogin(Suppliers $supplier){
        $supplier->login_status = ($supplier->login_status == 'authorized') ? 'unauthorized' : 'authorized';
        $supplier->save();

        return redirect()->back()->with('success', ($supplier->login_status == 'authorized') ? 'Login do fornecedor autorizado com sucesso.' : 'Login do fornecedor desativado com sucesso.');
    }

    public function toggleShipmentAddress(Suppliers $supplier){
        $supplier->use_shipment_address = ($supplier->use_shipment_address == 1) ? 0 : 1;
        $supplier->save();

        return redirect()->back()->with('success', ($supplier->use_shipment_address == 1) ? 'Utilização do endereço de remssa ativado com sucesso.' : 'Utilização do endereço de remessa desativado com sucesso.');
    }

    public function sendToSafe2Pay($supplier_id){
        $supplier = Suppliers::find($supplier_id);

        if($supplier){
            $service = new SafeToPayService();
            $return = $service->registerSupplierSubAccount($supplier);
            return redirect()->back()->with([$return['status'] => $return['message']]);
        }else{
            return redirect()->back()->with(['error' => 'Fornecedor não encontrado']);
        }
    }

    public function delete(Suppliers $supplier){
        if($supplier->delete()){
            return redirect()->back()->with('success', 'Fornecedor deletado com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu algum erro ao deletar este fornecedor, tente novamente em alguns minutos.');
        }
    }

    public function save($id, Request $request){
        $supplier = Suppliers::find($id);
        if($supplier){
            $supplier->mawa_post_tax = $request->mawa_post_tax;
            $supplier->save();

            return redirect()->back()->with('success', 'Fornecedor editado com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Falha ao editar fornecedor.');
        }
    }
}
