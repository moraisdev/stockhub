<?php

namespace App\Services;

use App\Exceptions\CustomException;

use App\Models\ProductVariants;
use App\Models\AliexpressProducts;
use App\Models\AliexpressProductVariants;
use App\Models\AliexpressProductOptions;
use App\Models\Images;
use App\Models\Suppliers;

class AliExpressService{

    public $supplier;

    public function __construct(Suppliers $supplier){
        $this->supplier = $supplier;

        if(!$this->supplier){
            throw new CustomException("Aconteceu um erro inesperado. Tente novamente em alguns minutos.", 404);            
        }
    }

    public function findDBProduct($ae_product_id){
        $product = AliexpressProducts::with('variants', 'options')->find($ae_product_id);

        if(!$product){
            throw new CustomException("Erro ao ligar produtos. Tente novamente através de nossa extensão do Google Chrome.", 500);
        }

        return $product;
    }

    public function getProductDetailsFromScript($script){           
        $replace_array = ["\n"];

        $script = str_replace($replace_array, '', $script);
        $script = preg_replace('/\s+(?=([^"]*"[^"]*")*[^"]*$)/', '', $script);
        $script = strstr($script, '{"actionModule"');
        $script = substr($script, 0, strpos($script, ",csrfToken:"));

        $ae_product = json_decode($script);

        return $this->createProductArray($ae_product);
    }

    public function createProductArray($ae_product){
        $product = [];

        $product['id'] = $ae_product->actionModule->productId;
        $product['title'] = $ae_product->titleModule->subject;
        $product['url_title'] = urlencode($ae_product->titleModule->subject);
        $product['min_cost'] = $ae_product->priceModule->minAmount->value;
        $product['max_cost'] = $ae_product->priceModule->maxAmount->value;
        $product['images'] = [];

        foreach ($ae_product->imageModule->imagePathList as $key => $path) {
            $product['images'][] = $path;
        }

        $properties = $this->getProductProperties($ae_product->skuModule->productSKUPropertyList);

        $product['options'] = $properties['titles'];
        $product['variants'] = $this->getProductSkus($ae_product->skuModule->skuPriceList, $properties, $ae_product->titleModule->subject);   

        return $product;

    }

    public function getProductProperties($properties_list){
        $titles = [];
        $values = [];

        foreach ($properties_list as $property) {

            foreach ($property->skuPropertyValues as $value) {
                $titles[$property->skuPropertyName][] = $value->propertyValueDisplayName;
                $values[$value->propertyValueId] = $value->propertyValueDisplayName;
            }
        }

        return ['titles' => $titles, 'values' => $values];
    }

    public function getProductSkus($sku_list, $properties, $title = ''){
        $skus = [];
        $count = 0;

        foreach ($sku_list as $sku_object) {
            $propIds = explode(',', $sku_object->skuPropIds);

            $sku_values = [];

            $sku_name = $title;

            foreach ($propIds as $propId) {
                $sku_values[] = $properties['values'][$propId];

                $sku_name .= ' - '.$properties['values'][$propId];
            }

            $skus[$count]['title'] = $sku_name;
            $skus[$count]['inventory'] = $sku_object->skuVal->inventory;
            $skus[$count]['cost'] = $sku_object->skuVal->skuActivityAmount->value;
            $skus[$count]['options'] = $sku_values;
            $skus[$count]['sku'] = $sku_object->skuAttr;

            $count++;
        }

        return $skus;
    }

    public function createAliExpressProduct($product){
        $ae_product = AliexpressProducts::firstOrNew(['aliexpress_id' => $product['id']]);

        if(isset($ae_product->id) && $ae_product->id != null){
            $ae_product->title = $product['title'];
            $ae_product->url_title = $product['url_title'];
            $ae_product->min_cost = $product['min_cost'];
            $ae_product->max_cost = $product['max_cost'];
            $ae_product->title = $product['title'];

            $ae_product->save();

            return $ae_product->id;
        }

        $ae_product->title = $product['title'];
        $ae_product->url_title = $product['url_title'];
        $ae_product->min_cost = $product['min_cost'];
        $ae_product->max_cost = $product['max_cost'];
        $ae_product->title = $product['title'];

        $images_ids = [];

        foreach ($product['images'] as $key => $path) {
            $new_image = Images::firstOrCreate(['supplier_id' => $this->supplier->id, 'type' => 'products', 'source' => $path]);

            $images_ids[] = $new_image->id;
        }

        $ae_product->images_ids = ($images_ids) ? implode(',', $images_ids) : null;

        $ae_product->save();

        $this->createAliExpressVariants($product['variants'], $ae_product->id);

        if($product['options']){
            $this->createAliExpressOptions($product['options'], $ae_product->id);
        }

        return $ae_product->id;
    }

    public function createAliExpressVariants($variants, $product_id){
        foreach ($variants as $variant) {
            $ae_variant = AliexpressProductVariants::firstOrNew(['product_id' => $product_id, 'sku' => $variant['sku']]);

            $ae_variant->title = $variant['title'];
            $ae_variant->price = $variant['cost'] * 2;
            $ae_variant->cost = $variant['cost'];
            $ae_variant->inventory = $variant['inventory'];
            $ae_variant->status = ($variant['inventory'] > 0) ? 'active' : 'inactive';
            $ae_variant->options = implode(',', $variant['options']);

            $ae_variant->save();
        }
    }

    public function createAliExpressOptions($options, $product_id){
        foreach ($options as $option => $values) {
            $ae_option = AliexpressProductOptions::firstOrNew(['product_id' => $product_id, 'option' => $option]);

            $ae_option->values = implode(',', $values);

            $ae_option->save();
        }
    }

    public function updateVariantSku($variant_id, $options_string){
        $ae_variant = AliexpressProductVariants::where('options', $options_string)->first();

        $sku = $ae_variant->sku;

        $variant = ProductVariants::find($variant_id);
        $variant->sku = $sku;

        $variant->save();

        return $variant;
    }
}