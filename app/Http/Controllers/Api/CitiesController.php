<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

use App\Models\Cities;

class CitiesController extends Controller
{
    public function getCitiesByState(Request $request){
        $uf = $request->uf;

        $cities = Cities::whereHas('state', function($query) use ($uf){
            $query->where('uf', $uf);
        })->get();

        return $cities;
    }

    public function getZipcode(Request $request){
        //faz a chamada na api e retorna o cep
        $client = new \GuzzleHttp\Client();

        $city = $request->city;
        $state = $request->state;
        $district = $request->district;
        
        if($city && $state){
            $response = $client->request('GET', 'https://viacep.com.br/ws/'.$state.'/'.$city.($district ? '/'.$district : '').'/json/');
        
            if($response->getStatusCode() == 200){
                $ceps = json_decode($response->getBody());

                if(count($ceps) > 0){
                    return $ceps;
                }
                return false;
            }
        }

        return false;
    }
}
