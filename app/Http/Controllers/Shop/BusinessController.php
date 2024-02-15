<?php
namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Models\ShopAddress;

use Auth;

class BusinessController extends Controller
{
    public function index(){
        
        return view('shop.profile.business');
    }

    public function update(Request $request){
        $shop = Auth::user();

        $shop->phone = preg_replace('/\D/', '', $request->phone);
        $shop->document = preg_replace('/\D/', '', $request->document);
        $shop->fantasy_name = $request->fantasy_name;
        $shop->corporate_name = $request->corporate_name;
        $shop->state_registration = $request->state_registration;

        $shop->save();

        $address = ShopAddress::firstOrNew(['shop_id' => $shop->id]);

        $address->street_company = $request->street_company;
        $address->number_company = $request->number_company;
        $address->district_company = $request->district_company;
        $address->complement_company = $request->complement_company;
        $address->city_company = $request->city_company;
        $address->state_code_company = $request->state_code_company;
        $address->country_company = $request->country_company;
        $address->zipcode_company = preg_replace('/\D/', '', $request->zipcode_company);

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
