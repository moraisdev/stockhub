<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tutorial;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tutorial = Tutorial::all();
        return view('admin.tutorial.index', compact('tutorial'));
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
     * @param  \App\Tutorial  $tutorial
     * @return \Illuminate\Http\Response
     */
    public function show(Tutorial $tutorial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tutorial  $tutorial
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tutorial = Tutorial::where('id' , $id )->first();
        return view('admin.tutorial.edit', compact('tutorial'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tutorial  $tutorial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        $tutorial = new Tutorial();

        $tutorial = Tutorial::find($request->id);
    	$tutorial->descricao = $request->descricao;
        $tutorial->link = $request->link;


    	if($tutorial->save()){
    		return redirect()->route('admin.tutorial.index')->with('success', 'Tutorial editado com sucesso.');
    	}else{
    		return redirect()->back()->with('error', 'Erro ao editar. Tente novamente em alguns minutos.')->withInput();
    	}       
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tutorial  $tutorial
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tutorial $tutorial)
    {
        //
    }
}
