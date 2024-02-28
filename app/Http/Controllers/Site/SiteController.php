<?php

namespace App\Http\Controllers\Site;

use App\Mail\ContactForm;
use App\Mail\Newsletter;
use App\Models\Receipts;
use Illuminate\Http\Request;
use Auth;
use Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Categories;
use App\Models\Products;

class SiteController extends Controller
{
    public function index(){
        return view('site.index.index');
    }

    public function downloadReceipt($customer_id, $receipt_id){
        $receipt = Receipts::where('customer_id', $customer_id)->find($receipt_id);

        if(!$receipt){
            echo 'Aconteceu algum erro ao efetuar o download de sua nota fiscal. Tente novamente mais tarde.';
            exit;
        }

        $file_name = str_replace(env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/'), '', $receipt->file);

        return Storage::disk('s3')->download($file_name);
    }

    public function contact_form(Request $request){
        Mail::to('contato@mawapost.com')->send(new ContactForm($request->name, $request->email, $request->message));

        return redirect()->back()->with(['success' => 'Contato enviado com sucesso.']);
    }

    public function newsletter(Request $request){
        Mail::to('contato@mawapost.com')->send(new Newsletter($request->email));

        return redirect()->back()->with(['success' => 'Seu e-mail foi adicionado à nossa newsletter.']);
    }
	
	public function catalog(){  

        $products_ids = [];
        $categories = Categories::get();
        $products  = Products::with('variants', 'supplier')->whereHas('supplier', function($q){
		    $q->where('status', 'active')->where('login_status', 'authorized');
        })->where('public', 1)->whereNotIn('id', $products_ids)->get();

        

        return view('shop.catalog.catalog', compact('categories' , 'products'));
    }
}
