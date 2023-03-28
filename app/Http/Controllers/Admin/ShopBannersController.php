<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Auth;
use App\Models\ShopBanner;
use Illuminate\Support\Str;

class ShopBannersController extends Controller
{
    public function index(){
        $banners = ShopBanner::orderBy('id', 'desc')->paginate(5);
        return view('admin.banners.index', compact('banners'));
    }

    public function create(){
        return view('admin.banners.create');
    }

    public function store(Request $request){
        $banner = new ShopBanner();

        $banner->name = $request->name;
        $banner->link = $request->link;

        if($request->hasFile('img_source')){
            $name = Str::random(15). '.' . $request->img_source->extension();

            $path = $request->img_source->storeAs('image_uploads', $name, 's3');

            $banner->img_source = env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/').$path;
        }

        if($request->hasFile('img_source_mobile')){
            $name = Str::random(15). '.' . $request->img_source_mobile->extension();

            $path = $request->img_source_mobile->storeAs('image_uploads', $name, 's3');

            $banner->img_source_mobile = env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/').$path;
        }

        if($banner->save()){
            return redirect()->route('admin.banners.index')->with('success', 'Banner cadastrado com sucesso.');
        }else{
            return redirect()->route('admin.banners.index')->with('error', 'Erro ao cadastrar o banner. Tente novamente em alguns minutos.');
        }
    }

    public function show($banner_id){
        $banner = ShopBanner::find($banner_id);

        return view('admin.banners.show', compact('banner'));
    }

    public function edit($banner_id){
        $banner = ShopBanner::find($banner_id);

        return view('admin.banners.edit', compact('banner'));
    }

    public function update(){

    }

    public function delete(){

    }
}
