<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Models\ShopRadar;

class RadarService
{
    public $supplier;

    public function __construct($supplier)
    {
        $this->supplier = $supplier;

        if (!$this->supplier) {
            throw new CustomException("Aconteceu um erro inesperado. Tente novamente em alguns minutos.", 404);
        }
    }

    public function find(int $id)
    {
        $shopRadar = ShopRadar::with('shop')->where('shop_id', $this->supplier->id)->find($id);
    
        if (!$shopRadar) {
            throw new CustomException("Radar não encontrado.", 404);
        }
    
        return $shopRadar;
    }
    
    public function update($id, Request $request)
    {
        $shopRadar = $this->find($id);
        $shopRadar->status = $request->status;
        $shopRadar->updated_at = $request->updated_at;

        if (!$shopRadar->save()) {
            throw new CustomException("Erro ao atualizar a importação coletiva. Tente novamente em alguns minutos.", 500);
        }

        return $shopRadar;
    }
}
