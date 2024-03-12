<?php
namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Models\ShopAddressBusiness;

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

        $address_business = ShopAddressBusiness::firstOrNew(['shop_id' => $shop->id]);

        $address_business->street_company = $request->street_company;
        $address_business->number_company = $request->number_company;
        $address_business->district_company = $request->district_company;
        $address_business->complement_company = $request->complement_company;
        $address_business->city_company = $request->city_company;
        $address_business->state_code_company = $request->state_code_company;
        $address_business->country_company = $request->country_company;
        $address_business->zipcode_company = preg_replace('/\D/', '', $request->zipcode_company);

        $address_business->save();

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso.');
    }

}
