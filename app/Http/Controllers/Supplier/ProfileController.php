<?php

namespace App\Http\Controllers\Supplier;

use App\Models\SupplierBank;
use Illuminate\Http\Request;
use App\Models\Admins;

use App\Models\SupplierAddress;

use Auth;

class ProfileController extends Controller
{
    public function index(){

        $admins = Admins::find(2);
        
    	return view('supplier.profile.index' , compact('admins'));
    }

    public function update(Request $request){
    	$supplier = Auth::user();
        $admins = Admins::find(2);
       
    	$supplier->name = $request->name;
        $supplier->phone = preg_replace('/\D/', '', $request->phone);
        $supplier->legal_name = $request->legal_name;
        $supplier->commercial_name = $request->commercial_name;
        $supplier->responsible_name = $request->responsible_name;
        $supplier->responsible_document = preg_replace('/\D/', '', $request->responsible_document);
        $supplier->document = preg_replace('/\D/', '', $request->document);
        $supplier->tech_name = $request->tech_name;
        $supplier->tech_document = preg_replace('/\D/', '', $request->tech_document);
        $supplier->tech_email = $request->tech_email;
        $supplier->geren_cliente_id = $request->geren_cliente_id;
        $supplier->geren_cliente_se = $request->geren_cliente_se;
        $supplier->geren_chave = $request->geren_chave;

      
     
        
        
        if($request->file('geren_pem')){
            $cert = $request->file('geren_pem');
            $nomecert = $request->file('geren_pem')->getClientOriginalName();
            $cert->move(public_path('certsger'), $nomecert);
            $supplier->geren_pem = $nomecert;
        }

        //$supplier->geren_pem = $request->geren_pem;

    	$supplier->save();

    	$address = SupplierAddress::firstOrNew(['supplier_id' => $supplier->id, 'type' => 'default']);

        
    	$address->street = $request->street;
        $address->number = $request->number;
        $address->district = $request->district;
        $address->complement = $request->complement;
    	$address->city = $request->city;
    	$address->state_code = $request->state_code;
    	$address->country = $request->country;
    	$address->zipcode = preg_replace('/\D/', '', $request->zipcode);

    	$address->save();

    	if($supplier->use_shipment_address && is_array($request->shipment)){
            $shipment_address = SupplierAddress::firstOrNew(['supplier_id' => $supplier->id, 'type' => 'shipment']);

            $shipment_address->street = $request->shipment['street'];
            $shipment_address->number = $request->shipment['number'];
            $shipment_address->district = $request->shipment['district'];
            $shipment_address->complement = $request->shipment['complement'];
            $shipment_address->city = $request->shipment['city'];
            $shipment_address->state_code = $request->shipment['state_code'];
            $shipment_address->country = $request->shipment['country'];
            $shipment_address->zipcode = preg_replace('/\D/', '', $request->shipment['zipcode']);

            $shipment_address->save();
        }

        if ($admins->plano_f == 2) {
        $bank = SupplierBank::firstOrNew(['supplier_id' => $supplier->id]);

        $bank->code = $request->bank['code'];
        $bank->account_type = $request->bank['account_type'];
        $bank->agency = preg_replace('/\D/', '', $request->bank['agency']);
        $bank->agency_digit = $request->bank['agency_digit'] ? preg_replace('/\D/', '', $request->bank['agency_digit']) : null;
        $bank->account = preg_replace('/\D/', '', $request->bank['account']);
        $bank->account_digit = $request->bank['agency_digit'] ? preg_replace('/\D/', '', $request->bank['account_digit']) : null;

    	$bank->save();
        }
    	return redirect()->back()->with('success', 'Perfil atualizado com sucesso.');
    }

    public function toggleStatus(){
        $supplier = Auth::user();

        $supplier->status = ($supplier->status == 'active') ? 'inactive' : 'active';
        $supplier->save();

        return redirect()->back()->with('success', ($supplier->status == 'active') ? 'Atividades reiniciadas com sucesso.' : 'Atividades pausadas com sucesso.');
    }
}

