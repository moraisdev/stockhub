<?php

namespace App\Services;

use App\Exceptions\CustomException;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/* Models */
use App\Models\Shops;
use App\Models\Products;
use App\Models\ProductVariants;
use App\Models\ProductVariantStock;
use App\Models\ProductVariantOptionsValues;

use Hash;
use Auth;

class ProductVariantsService{

    public $product;

    public function __construct(Products $product = null, Shops $shop = null){
        $this->product = $product;
        $this->shop = $shop;
    }

    public function get(){
        if(!$this->shop){
            return false;
        }

        if($this->product){
            return ProductVariants::with('product')
                            ->select('product_variants.*')
                            ->leftJoin('products', 'products.id', '=', 'product_variants.product_id')
                            ->leftJoin('shop_products', 'shop_products.product_id', '=', 'products.id')
                            ->where('product_variants.product_id', $this->product->id)
                            ->where('shop_products.shop_id', $this->shop->id)
                            ->get();
        }else{
            return ProductVariants::with('product')
                            ->select('product_variants.*')
                            ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
                            ->leftJoin('shop_products', 'products.id', '=', 'shop_products.product_id')
                            ->where('shop_products.shop_id', $this->shop->id)
                            ->get();
        }
    }

    public function getAliExpressReadyVariants($supplier_id){
        return ProductVariants::select('product_variants.*')
                              ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
                              ->where('products.supplier_id', $supplier_id)
                              ->where('product_variants.sku', '!=', '')
                              ->whereNotNull('product_variants.sku')
                              ->get();
    }

    public function getByIdsArray($ids_array, $supplier_id){
        return ProductVariants::select('product_variants.*')
                              ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
                              ->where('products.supplier_id', $supplier_id)
                              ->whereIn('product_variants.id', $ids_array)
                              ->get();
    }

    public function find($id){
        if(!$this->shop){
            $variant = ProductVariants::where('product_id', $this->product->id)->find($id);
            if($variant){
                return $variant;
            }
            else{
                throw new CustomException("Variant not found.", 404);
            }
        }else{
            if($this->product){
                return ProductVariants::with('product')
                    ->select('product_variants.*')
                    ->leftJoin('products', 'products.id', '=', 'product_variants.product_id')
                    ->leftJoin('shop_products', 'shop_products.product_id', '=', 'products.id')
                    ->where('product_variants.product_id', $this->product->id)
                    ->where('shop_products.shop_id', $this->shop->id)
                    ->find($id);
            }else{
                return ProductVariants::with('product')
                    ->select('product_variants.*')
                    ->leftJoin('products', 'products.id', '=', 'product_variants.product_id')
                    ->leftJoin('shop_products', 'shop_products.product_id', '=', 'products.id')
                    ->where('shop_products.shop_id', $this->shop->id)
                    ->find($id);
            }
        }
    }

    public function updateWithArray($variant_id, $data){
        ProductVariants::where('product_id', $this->product->id)->where('id', $variant_id)->update($data);
    }

    public function createFromAliexpress($ae_variant, $options_ids){
        $variant = new ProductVariants();

        $variant->product_id = $this->product->id;
        $variant->title = $ae_variant->title;
        $variant->price = $ae_variant->price;
        /*$variant->cost = $ae_variant->cost;*/
        $variant->sku = $ae_variant->sku;
        $variant->published = ($ae_variant->status == 'active') ? 1 : 0;

        if($variant->save()){
            $variant->url_hash = md5(uniqid($variant->id, true));
            $variant->save();

            $options = ($ae_variant->options) ? explode(',', $ae_variant->options) : null;

            $this->updateVariantStock($variant->id, 0);

            if($options){
                $this->createVariantOptionsValues($variant->id, $options, $options_ids);
            }

            return $variant;
        }else{
            throw new CustomException("Erro ao cadastrar variante. Tente novamente em alguns minutos.", 500);

        }
    }

    public function create($fields, $options_ids){
        $variant = new ProductVariants();

        $variant->product_id = $this->product->id;
        $variant->title = $this->product->title;
        $variant->price = $fields->price;

        if($variant->product->supplier->id == 56){ 
            $variant->internal_cost = $fields->internal_cost;
        }

        $variant->weight_in_grams = $fields->weight_in_grams;
        $variant->width = $fields->width;
        $variant->height = $fields->height;
        $variant->depth = $fields->depth;
        $variant->sku = $fields->sku;

        if(isset($fields->img_source) && $fields->img_source){
            $img_source = $fields->img_source;
    
            $imageData = file_get_contents($img_source->getRealPath());
            $encodedData = base64_encode($imageData);
    
            $variant->img_source_data = $encodedData;
        }else{
            $variant->img_source_data = $this->product->img_source_data;
        }

        if($variant->save()){
            $variant->url_hash = md5(uniqid($variant->id, true));
            $variant->save();

            if($fields->stock > 0){
                $this->updateVariantStock($variant->id, $fields->stock);
            }else{
                $this->updateVariantStock($variant->id, 0);
            }

            if(isset($fields->options) && $fields->options){
                $this->createVariantOptionsValues($variant->id, $fields->options);
            }
            if(isset($fields->new_options) && $fields->new_options){
                $this->createVariantOptionsValues($variant->id, $fields->new_options, $options_ids);
            }

            $this->updateVariantTitle($variant->id);

            return $variant;
        }else{
            throw new CustomException("Erro ao cadastrar variante. Tente novamente em alguns minutos.", 500);

        }
    }

    public function update($variant_id, $fields, $options_ids){
        $variant = $this->find($variant_id);

        $variant->price = $fields->price;
        if($this->product->supplier_id == 56){ 
            $variant->internal_cost = $fields->internal_cost;
        }

        $variant->weight_in_grams = $fields->weight_in_grams;
        $variant->width = $fields->width;
        $variant->height = $fields->height;
        $variant->depth = $fields->depth;
        $variant->sku = $fields->sku;

        if(isset($fields->img_source) && $fields->img_source){
            $img_source = $fields->img_source;
    
            $imageData = file_get_contents($img_source->getRealPath());
            $encodedData = base64_encode($imageData);
    
            $variant->img_source_data = $encodedData;
        }else{
            $variant->img_source_data = $this->product->img_source_data;
        }

        if($variant->save()){
            if($fields->stock > 0){
                $this->updateVariantStock($variant->id, $fields->stock);
            }else{
                $this->updateVariantStock($variant->id, 0);
            }

            if(isset($fields->new_options) && $fields->new_options){
                $this->createVariantOptionsValues($variant->id, $fields->new_options, $options_ids);
            }

            if(isset($fields->options) && $fields->options){
                $this->updateVariantOptionsValues($variant->id, $fields->options);
            }

            $this->updateVariantTitle($variant->id);

            return $variant;
        }else{
            throw new CustomException("Erro ao atualizar variante. Tente novamente em alguns minutos.", 500);

        }
    }

    public function createVariantOptionsValues($variant_id, $new_options, $options_ids = null){
        foreach ($new_options as $option_key => $value) {
            $option_id = $option_key;

            if($options_ids){
                $option_id = $options_ids->where('option_key', $option_key)->first()['option_id'];
            }

            $option_value = new ProductVariantOptionsValues();

            $option_value->product_variant_id = $variant_id;
            $option_value->product_option_id = $option_id;
            $option_value->value = $value;

            if(!$option_value->save()){
                throw new CustomException("Erro ao atualizar uma das opções das variantes. Tente novamente em alguns minutos.", 500);
            }
        }
    }

    public function updateVariantOptionsValues($variant_id, $options){
        foreach ($options as $option_id => $value) {
            $verify = ProductVariantOptionsValues::where('product_variant_id', $variant_id)
                                                 ->where('product_option_id', $option_id)
                                                 ->update(['value' => $value]);
        }
    }

    public function deleteWhereNotIn($ids_array){
        $variants_ids = ProductVariants::where('product_id', $this->product->id)->whereNotIn('id', $ids_array)->pluck('id');

        if($variants_ids){
            $variants_ids = $variants_ids->toArray();

            ProductVariants::whereIn('id', $variants_ids)->delete();
            ProductVariantOptionsValues::whereIn('product_variant_id', $variants_ids)->delete();
        }

    }

    public function updateVariantTitle($variant_id){
        $variant = ProductVariants::with('options_values')->find($variant_id);

        if(!$variant){
            throw new CustomException("Erro ao gerar o título da variante. Tente novamente em alguns minutos.", 500);
        }

        $options_title = '';

        if($variant->options_values){
            foreach ($variant->options_values as $option_value) {
                $options_title .= $option_value->value.' - ';
            }

            $options_title = substr($options_title, 0, -2);
        }

        if($options_title != ''){
            $variant->title = $this->product->title . ' - ' . $options_title;

            if(!$variant->save()){
                throw new CustomException("Erro ao gerar o título da variante. Tente novamente em alguns minutos.", 500);
            }
        }
    }

    public function updateVariantStock($variant_id, $quantity){
        $stock = ProductVariantStock::firstOrNew(['product_variant_id' => $variant_id]);

        $stock->quantity = $quantity;

        $stock->save();
    }
}
