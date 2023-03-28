<?php
namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Models\ShopAddress;
use App\Models\Admins;


use Auth;

class ProfileController extends Controller
{
    public function index(){
        
        return view('shop.profile.index');
    }

    public function update(Request $request){
        $shop = Auth::user();

        $shop->name = $request->name;
        $shop->responsible_name = $request->responsible_name;
        $shop->phone = preg_replace('/\D/', '', $request->phone);
        $shop->document = preg_replace('/\D/', '', $request->document);
        $shop->fantasy_name = $request->fantasy_name;
        $shop->corporate_name = $request->corporate_name;
        $shop->state_registration = $request->state_registration;

        $shop->save();

        $address = ShopAddress::firstOrNew(['shop_id' => $shop->id]);

        $address->street = $request->street;
        $address->number = $request->number;
        $address->district = $request->district;
        $address->complement = $request->complement;
        $address->city = $request->city;
        $address->state_code = $request->state_code;
        $address->country = $request->country;
        $address->zipcode = preg_replace('/\D/', '', $request->zipcode);

        $address->save();

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso.');
    }
    
     public function updatebling(Request $request){
        $shop = Auth::user();

        $shop->bling_apikey  = $request->bling_apikey;
        $shop->save();

        return redirect()->back()->with('success', 'Bling Api atualizado com sucesso.');
    }

}
