<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shopplano;
use Illuminate\Http\Request;
use App\Models\Fornecedorplano;
use App\Models\Admins;
use App\Models\Store_invoice; 
use App\Models\Supplier_invoice;

class ShopplanoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $planos = Shopplano::all();
        $planosforn = Fornecedorplano::all();
        $admins = Admins::find(2);
       
        return view('admin.planos.index', compact('planos' , 'planosforn','admins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\shop\Shopplano  $shopplano
     * @return \Illuminate\Http\Response
     */
    public function show(Shopplano $shopplano)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\shop\Shopplano  $shopplano
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $planos = Shopplano::where('id' , $id )->first();
        return view('admin.planos.edit', compact('planos'));
    }

    public function editf($id)
    {
        $planosf = fornecedorplano::where('id' , $id )->first();
        return view('admin.planos.editf', compact('planosf'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\shop\Shopplano  $shopplano
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $Shopplano = new Shopplano();

        $Shopplano = Shopplano::find($request->id);
    	$Shopplano->descricao = $request->descricao;
        $Shopplano->titulo = $request->titulo;
        $Shopplano->valor = strtr($request->valor, ',', '.');
        $Shopplano->ciclo = $request->ciclo;
        $Shopplano->status = $request->status;
        $Shopplano->destaque = $request->destaque;



    	if($Shopplano->save()){
    		return redirect()->route('admin.planos.index')->with('success', 'Plano editado com sucesso.');
    	}else{
    		return redirect()->back()->with('error', 'Erro ao editar. Tente novamente em alguns minutos.')->withInput();
    	}
    }

    public function updatef(Request $request)
    {
        $forplano = new Fornecedorplano();

        $forplano = Fornecedorplano::find($request->id);
    	$forplano->descricao = $request->descricao;
        $forplano->titulo = $request->titulo;
        $forplano->valor = strtr($request->valor, ',', '.');
        $forplano->ciclo = $request->ciclo;
        $forplano->status = $request->status;
        $forplano->destaque = $request->destaque;



    	if($forplano->save()){
    		return redirect()->route('admin.planos.index')->with('success', 'Plano  editado com sucesso.');
    	}else{
    		return redirect()->back()->with('error', 'Erro ao editar. Tente novamente em alguns minutos.')->withInput();
    	}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\shop\Shopplano  $shopplano
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shopplano $shopplano)
    {
        //
    }

    public function payconfig()    {
    
        $admins = Admins::find(2);        
    	return view('admin.planos.pay_config' , compact('admins'));

    }

    public function payconfigup(Request $request)   
     {
            
    // $messages = [
    //    'mimetypes' => 'Adcione o certificado valido com extensão PEM.',
    //    'required' => 'Adcione o certificado valido com extensão PEM.',

   // ];

    //$request->validate([
   //     'geren_pem'=>'required|mimetypes:pem'
    //], $messages);
     
       $admins = new Admins();
        $admins = $admins::find(2);

        $admins->geren_cliente_id = $request->geren_cliente_id;
        $admins->geren_cliente_se = $request->geren_cliente_se;
        $admins->geren_chave = $request->geren_chave;

       // $file = $request->file('geren_pem'); 

       
        //if (is_null($file)) {
        //    return redirect()->route('admin.plans.payconfig')->with('error', 'Erro adcione o certificado gerencianet.');
        ///}
        
        if($request->file('geren_pem')){
            $cert = $request->file('geren_pem');
            $nomecert = $request->file('geren_pem')->getClientOriginalName();
            $extension = $request->file('geren_pem')->getClientOriginalExtension();
            if ($extension == "pem" || $extension == "PEM") {
                $cert->move(public_path('certsger'), $nomecert);
                $admins->geren_pem = $nomecert;
            }else {
                return redirect()->route('admin.plans.payconfig')->with('error', 'O Arquivo deve ser um arquivo do tipo:PEM');
                   
             }        
        }

        if($admins->save()){
            return redirect()->route('admin.plans.payconfig')->with('success', 'Pagamento cadastrado com sucesso.');
        }else{
            return redirect()->route('admin.plans.payconfig')->with('error', 'Erro ao cadastrar o pagamento. Tente novamente em alguns minutos.');
        }
        

    }

    public function paid()   
    {
        $mes = date("m");
        $admins = Admins::find(2);
        $assinaturashop = Store_invoice::where('plan','<>' ,'FREE')->whereMonth('updated_at', $mes)->where('payment','paid')->orderBy('updated_at','desc')->get();
        $assinaturafor = Supplier_invoice::where('plan','<>' ,'FREE')->whereMonth('updated_at', $mes)->where('payment','paid')->orderBy('updated_at','desc')->get();
       
        return view('admin.planos.paid', compact('assinaturashop', 'assinaturafor', 'admins' ) );


    }    
}
