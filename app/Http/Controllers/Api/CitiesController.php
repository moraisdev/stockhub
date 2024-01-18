<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use GuzzleHttp\Client;

class CitiesController extends Controller
{
    public function getCitiesByState(Request $request){
        $uf = $request->uf;
        $client = new Client();

        try {
            $url = 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/' . $uf . '/municipios';
            $response = $client->request('GET', $url);

            if ($response->getStatusCode() == 200) {
                $cities = json_decode($response->getBody());

                $formattedCities = array_map(function($city) {
                    return [
                        'id' => $city->id,
                        'name' => $city->nome
                    ];
                }, $cities);

                return response()->json($formattedCities);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao conectar com a API do IBGE.', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Parâmetros insuficientes para a busca de cidades.'], 400);
    }
}