<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Models\Shops;
use App\Models\Orders;
use App\Models\ShopContractedPlans;

use Auth;

class ShopsController extends Controller
{
    public function index(){
    	$shops = Shops::paginate(100);
    
    
        $orderPending = Orders::where("status", "pending")->count();
        $orderPaid = Orders::where("status", "paid")->count();
        
    	return view('admin.shops.index', compact('shops', 'orderPending', 'orderPaid'));
    }

    public function search(Request $request){
        $orderPending = Orders::where("status", "pending")->count();
        $orderPaid = Orders::where("status", "paid")->count();
        $shops = Shops::where('name', 'like', '%'.$request->query('query').'%')
            ->orWhere('email', 'like', '%'.$request->query('query').'%')
            ->orWhere('phone', 'like', '%'.$request->query('query').'%')
            ->paginate(100);
        return view('admin.shops.index', compact('shops', 'orderPending', 'orderPaid'));
    }

    public function moreDaysFree(Shops $shop){
        $dataat = date("Y-m-d");
        $shop->created_at = date("Y-m-d H:i:s");
        $shop->status = 'active';

        $shopplano = ShopContractedPlans::where('shop_id', $shop->id)->first();
        $shopplano->subscription_status =  'active';
        $shopplano->due_date = date("Y-m-d", strtotime($dataat.'+ 14 days')); 
        $shopplano->save();

        if($shop->save()){
            return redirect()->back()->with('success', 'O lojista '.$shop->name.' agora tem mais 14 dias grátis.');
        }else{
            return redirect()->back()->with('error', 'Erro ao liberar mais dias grátis.');
        }
    }

    public function show(Shops $shop){
    	return view('admin.shops.show', compact('shop'));
    }

    public function login(Shops $shop){
    	Auth::guard('shop')->login($shop);

    	return redirect()->route('shop.dashboard');
    }

    public function toggleStatus(Shops $shop){
        $shop->status = ($shop->status == 'active') ? 'inactive' : 'active';
        $shop->save();

        return redirect()->back()->with('success', ($shop->status == 'active') ? 'Pagamento do lojista confirmado com sucesso.' : 'Pagamento do lojista revertido com sucesso.');
    }

    public function toggleLogin(Shops $shop){
        $shop->login_status = ($shop->login_status == 'authorized') ? 'unauthorized' : 'authorized';
        $shop->save();

        return redirect()->back()->with('success', ($shop->login_status == 'authorized') ? 'Login do lojista autorizado com sucesso.' : 'Login do lojista desativado com sucesso.');
    }

    public function delete(Shops $shop){
        if($shop->delete()){
            return redirect()->back()->with('success', 'Lojista deletado com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu algum erro ao deletar este lojista, tente novamente em alguns minutos.');
        }
    }

    public function deleteCard(Shops $shop){
        if($shop->token_card->delete()){
            return redirect()->back()->with('success', 'Cartão deletado com sucesso.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu algum erro ao deletar este cartão, tente novamente em alguns minutos.');
        }
    }
}
