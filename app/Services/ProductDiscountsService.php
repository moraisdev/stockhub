<?php

namespace App\Services;

use App\Exceptions\CustomException;

/* Models */
use App\Models\Products;
use App\Models\ProductVariants;
use App\Models\ProductDiscounts;
use App\Models\ProductVariantOptionsValues;

class ProductDiscountsService{

    public $product;

    public function __construct(Products $product){
        $this->product = $product;

        if(!$this->product){
            throw new CustomException("Aconteceu um erro inesperado. Tente novamente em alguns minutos.", 404);
        }
    }

    public function find($id){
        $discount = ProductDiscounts::where('product_id', $this->product->id)->find($id);

        if($discount){
            return $discount;
        }else{
            throw new CustomException("Desconto não encontrado.", 404);
            
        }
    }

    public function create($fields){

        if(isset($fields->quantity) && $fields->quantity > 0 && isset($fields->value) && $fields->value > 0){
            $discount = new ProductDiscounts();

            $discount->product_id = $this->product->id;
            $discount->quantity = $fields->quantity;
            $discount->value = $fields->value;

            if($discount->save()){
                return $discount;
            }else{
                throw new CustomException("Erro ao cadastrar um novo desconto.", 500);
            }
        }else{
            throw new CustomException("Campos vazios nos descontos.", 500);
        }
    }

    public function update($discount_id, $quantity, $value){
        $discount = $this->find($discount_id);

        $discount->quantity = $quantity;
        $discount->value = $value;

        if($discount->save()){
            return $discount;
        }else{
            throw new CustomException("Erro ao atualizar um dos descontos.", 500);
        }
    }

    public function deleteWhereNotIn($ids_array){
        //$variants_ids = ProductVariants::where('product_id', $this->product->id)->pluck('id')->toArray();

        ProductDiscounts::where('product_id', $this->product->id)->whereNotIn('id', $ids_array)->delete();
        //ProductVariantOptionsValues::whereIn('product_variant_id', $variants_ids)->whereNotIn('product_discount_id', $ids_array)->delete();
    }
}