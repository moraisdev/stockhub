<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AffiliateLink;
use Illuminate\Support\Str;

use App\Models\Shops;

class AffiliateLinkController extends Controller
{
    public function index(){
        $links = AffiliateLink::orderBy('id', 'desc')->paginate(100);
        
        return view('admin.affiliate-link.index', compact('links'));
    }

    public function create(){
        return view('admin.affiliate-link.create');
    }

    public function store(Request $request){
        // //gera o token
        // $arrChars = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w','x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W','X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        // $tamToken = 8;        
        // $affiliate = NULL;
        
        // do{
        //     $token = '';
        //     for ($i=0; $i < $tamToken; $i++) { 
        //         $token .= $arrChars[rand(0, 61)];
        //     }

        //     $affiliate = AffiliateLink::firstOrCreate(['name' => $request->name, 'type' => $request->type, 'token' => $token]);
        // }while(!$affiliate);s

        $token = Str::slug($request->name, '-');
        $affiliate = AffiliateLink::firstOrCreate(['name' => $request->name, 'type' => $request->type, 'token' => $token]);

        if($affiliate){
            return redirect()->route('admin.affiliate-link.index')->with('success', 'Afiliado cadastrado com sucesso.');
        }else{
            return redirect()->route('admin.affiliate-link.index')->with('error', 'Erro ao cadastrar o affiliate. Tente novamente em alguns minutos.');
        }
    }

    public function show($id){
        $link = AffiliateLink::find($id);
        
        $shops = array();
        $countAtivos = 0;

        foreach($link->registered_users as $registeredUser) {
            $shop = Shops::find($registeredUser->shop_id);
            $status = 'Inativo';
            
            if($shop){
                if($shop->contracted_plan && $shop->contracted_plan->subscription_status){
                    $status = $shop->contracted_plan->subscription_status;

                    if($shop->contracted_plan->subscription_status == 'Ativa'){
                        $countAtivos++;
                    }                    
                }

                array_push($shops, (object)[
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'email' => $shop->email,
                    'status' => $status,
                    'created_at' => $shop->created_at
                ]);
            }
        }      

        return view('admin.affiliate-link.show', compact('link', 'shops', 'countAtivos'));
    }

    public function edit(Request $request){
        
    }

    public function destroy($id){
        $link = AffiliateLink::find($id);

        if($link->delete()){
            return redirect()->route('admin.affiliate-link.index')->with('success', 'Link afiliado excluído com sucesso.');
        }
        return redirect()->route('admin.affiliate-link.index')->with('error', 'Erro ao excluir link afiliado. Tente novamente em alguns minutos.');
    }
}
