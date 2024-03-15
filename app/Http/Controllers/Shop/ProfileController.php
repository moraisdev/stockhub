<?php
namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;

use App\Models\ShopAddress;
use App\Models\Shops;

use Auth;

class ProfileController extends Controller
{
    public function index(){

        return view('shop.profile.index');
    }

    public function update(Request $request){
        $shop = Auth::user();
        $documentCleaned = preg_replace('/\D/', '', $request->input('responsible_document'));

        $documentExists = Shops::where('responsible_document', $documentCleaned)
                           ->where('id', '<>', $shop->id) // Exclui a loja atual da verificação
                           ->exists();


        if ($documentExists) {
            // Retorna para a página anterior com uma mensagem de erro se o responsible_document já existir
            return redirect()->back()->with('error', 'O CPF fornecido já está em uso por outra loja.');
        }

        $shop->responsible_name = $request->input('responsible_name');
        $shop->phone = preg_replace('/\D/', '', $request->phone);
        $shop->responsible_document = $documentCleaned;

        if ($request->hasFile('img_profile')) {
            $imageName = $shop->id.'_profile_'.time().'.'.$request->img_profile->extension();
            $request->img_profile->storeAs('profiles', $imageName, 'public');
            $shop->img_profile = $imageName;
        }

        $shop->save();

        $address = ShopAddress::updateOrCreate(
            ['shop_id' => $shop->id],
            [
                'street' => $request->input('street'),
                'number' => $request->input('number'),
                'district' => $request->input('district'),
                'complement' => $request->input('complement', ''),
                'zipcode' => preg_replace('/\D/', '', $request->input('zipcode')),
                'country' => $request->input('country'),
                'state_code' => $request->input('state_code'),
                'city' => $request->input('city')
            ]
        );

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso.');
    }

}
