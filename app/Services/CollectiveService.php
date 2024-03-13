<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\CollectiveImport;

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
            $collectiveImport = CollectiveImport::where('shop_id', $this->supplier->id)->find($id);

        if (!$collectiveImport) {
            throw new CustomException("Importação coletiva não encontrada.", 404);
        }

        return $collectiveImport;
    }
}