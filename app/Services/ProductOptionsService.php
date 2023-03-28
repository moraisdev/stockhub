<?php

namespace App\Services;

use App\Exceptions\CustomException;

/* Models */
use App\Models\Products;
use App\Models\ProductVariants;
use App\Models\ProductOptions;
use App\Models\ProductVariantOptionsValues;

class ProductOptionsService{

    public $product;

    public function __construct(Products $product){
        $this->product = $product;

        if(!$this->product){
            throw new CustomException("Aconteceu um erro inesperado. Tente novamente em alguns minutos.", 404);
        }
    }

    public function find($id){
        $option = ProductOptions::where('product_id', $this->product->id)->find($id);

        if($option){
            return $option;
        }else{
            throw new CustomException("Opção não encontrada.", 404);
            
        }
    }

    public function create($name){
        $option = new ProductOptions();

        $option->product_id = $this->product->id;
        $option->name = $name;

        if($option->save()){
            return $option;
        }else{
            throw new CustomException("Erro ao cadastrar uma nova opção.", 500);
        }
    }

    public function update($option_id, $name){
        $option = $this->find($option_id);

        $option->name = $name;

        if($option->save()){
            return $option;
        }else{
            throw new CustomException("Erro ao atualizar uma das opções.", 500);
        }
    }

    public function deleteWhereNotIn($ids_array){
        $variants_ids = ProductVariants::where('product_id', $this->product->id)->pluck('id')->toArray();

        ProductOptions::where('product_id', $this->product->id)->whereNotIn('id', $ids_array)->delete();
        ProductVariantOptionsValues::whereIn('product_variant_id', $variants_ids)->whereNotIn('product_option_id', $ids_array)->delete();
    }
}