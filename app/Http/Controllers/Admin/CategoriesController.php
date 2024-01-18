<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Models\Categories;

class CategoriesController extends Controller
{
    public function index(){
    	$categories = Categories::all();

    	return view('admin.categories.index', compact('categories'));
    }

    public function create(){
    	return view('admin.categories.create');
    }

    public function edit(Categories $category){
    	return view('admin.categories.edit', compact('category'));
    }

    public function store(Request $request){
    	$category = new Categories();

    	$category->name = $request->name;

    	if($category->save()){
    		return redirect()->route('admin.categories.index')->with('success', 'Categoria cadastrada com sucesso.');
    	}else{
    		return redirect()->back()->with('error', 'Erro ao cadastrar categoria. Tente novamente em alguns minutos.')->withInput();
    	}
    }

    public function update(Categories $category, Request $request){
    	$category->name = $request->name;

    	if($category->save()){
    		return redirect()->route('admin.categories.index')->with('success', 'Categoria atualizada com sucesso.');
    	}else{
    		return redirect()->back()->with('error', 'Erro ao atualizar categoria. Tente novamente em alguns minutos.')->withInput();
    	}
    }

    public function destroy(Categories $category){
    	if($category->delete()){
    		return redirect()->route('admin.categories.index')->with('success', 'Categoria excluída com sucesso.');
    	}else{
    		return redirect()->back()->with('error', 'Erro ao excluir categoria. Tente novamente em alguns minutos.')->withInput();
    	}
    }
}
