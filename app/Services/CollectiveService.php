<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Models\CollectiveImport;
use App\Models\Shops;

class CollectiveService
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
        $collectiveImport = CollectiveImport::with('shop.address')->where('shop_id', $this->supplier->id)->find($id);
        
        if (!$collectiveImport) {
            throw new CustomException("Importação coletiva não encontrada.", 404);
        }
    
        return $collectiveImport;
    }
    
    public function update($id, Request $request)
    {
        $collectiveImport = $this->find($id);
        $collectiveImport->produto_link = $request->produto_link;
        $collectiveImport->status = $request->status;
        $collectiveImport->rejection_reason = $request->rejection_reason;
        $collectiveImport->delivery_deadline = $request->delivery_deadline;
        $collectiveImport->cost_price = $request->cost_price;
        $collectiveImport->tracking_code = $request->tracking_code;
        $collectiveImport->updated_at = $request->updated_at;

        if (!$collectiveImport->save()) {
            throw new CustomException("Erro ao atualizar a importação coletiva. Tente novamente em alguns minutos.", 500);
        }

        return $collectiveImport;
    }
}
