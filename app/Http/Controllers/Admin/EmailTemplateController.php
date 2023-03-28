<?php

namespace App\Http\Controllers\Admin;
use App\Models\EmailTemplate;

use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $emails = EmailTemplate::all();
        return view('admin.emailtemplate.index', compact('emails'));
    }

    public function edit($id)
    {
        $emails = EmailTemplate::where('id' , $id )->first();
        return view('admin.emailtemplate.edit', compact('emails'));
    }

    public function update(Request $request)
    {
        
        $emails = new EmailTemplate();

        $emails = EmailTemplate::find($request->id);
    	$emails->template = $request->template;
       
    	if($emails->save()){
    		return redirect()->route('admin.emailtemplate.index')->with('success', 'Email editado com sucesso.');
    	}else{
    		return redirect()->back()->with('error', 'Erro ao editar. Tente novamente em alguns minutos.')->withInput();
    	}       
        
    }
}
