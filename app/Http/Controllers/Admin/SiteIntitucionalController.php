<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\site_intitucional;

class SiteIntitucionalController extends Controller
{
    public function index(){
    	$institucional = site_intitucional::find(1);

    	return view('admin.dashboard.institucional', compact('institucional'));
    }

    public function update(Request $request){
    	$institucional = site_intitucional::find(1);

       
       
		$institucional->tituloprincipal = $request->tituloprincipal;
		$institucional->tituloprincipal2 = $request->tituloprincipal2;
		$institucional->titulobutaoaderir = $request->titulobutaoaderir;
		$institucional->titulobeneficios = $request->titulobeneficios;
		$institucional->tituloservicos = $request->tituloservicos;
		$institucional->servicos = $request->servicos;
		$institucional->faq1 = $request->faq1;
		$institucional->descricaofaq1 = $request->descricaofaq1;
		$institucional->faq2 = $request->faq2;
		$institucional->descricaofaq2 = $request->descricaofaq2;
		$institucional->faq3 = $request->faq3;
		$institucional->descricaofaq3 = $request->descricaofaq3;
		$institucional->faq4 = $request->faq4;
		$institucional->descricaofaq4 = $request->descricaofaq4;
		$institucional->faq5 = $request->faq5;
		$institucional->descricaofaq5 = $request->descricaofaq5;
		
		$institucional->endereco = $request->endereco;
		$institucional->telefone1 = $request->telefone1;
		$institucional->telefone2 = $request->telefone2;
		$institucional->email1 = $request->email1;
		$institucional->email2 = $request->email2;
			
		
	    
       
        if($institucional->save()){
    		return redirect()->back()->with('success', 'Site editado com sucesso.');
    	}else{
    		return redirect()->back()->with('error', 'Erro ao editar. Tente novamente em alguns minutos.')->withInput();
    	}   


    	
    }
}
