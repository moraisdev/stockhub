<?php

namespace App\Services;

use App\Exceptions\CustomException;

/* Models */
use App\Models\Suppliers;
use App\Models\Products;
use App\Models\ProductOptions;
use App\Models\ProductVariants;
use App\Models\ProductVariantInventories;
use App\Models\ProductVariantOptionsValues;

class ProductsImportService{

    public $supplier;

    public function __construct(Suppliers $supplier){
        $this->supplier = $supplier;

        if(!$this->supplier){
            throw new CustomException("Aconteceu um erro inesperado. Tente novamente em alguns minutos.", 404);
        }
    }

    public function create(array $product){
        $product_object = (object) $product;

        /* Creates the product */
        $db_product = $this->createProduct($product_object);

        $options_ids_by_key_prefix = null;

        /* Creates the product options*/
        if($product['options']){
            $options_ids_by_key_prefix = $this->createOptions($db_product->id, $product['options']);
        }

        /* Creates the product variants */
        $this->createVariants($db_product->id, $product, $options_ids_by_key_prefix);
    }

    public function getCsvArrayOptionsNames(array $csv_array){
        $key_count = 1;

        $options = [];

        $key_option_name = 'option1_name';
        $key_option_value = 'option1_value';

        // Option must not be empty or have a default value
        while(isset($csv_array[$key_option_name]) && $csv_array[$key_option_value] != 'Default Title' && !empty($csv_array[$key_option_name])){
            $options[] = [
                'name' => $csv_array[$key_option_name],
                'key_prefix' => 'option' . $key_count
            ];

            $key_count++;

            $key_option_name = 'option' . $key_count . '_name';
            $key_option_value = 'option' . $key_count . '_value';
        }

        return ($options) ? $options : null;
    }

    public function generateVariantArray(array $variant, $options, $product_title = null){
        $new_variant = [];

        $new_variant['published'] = $variant['published'];
        $new_variant['title'] = $product_title;
        $new_variant['cost'] = $variant['cost_per_item'] ? $variant['cost_per_item'] : 0;
        $new_variant['img_source'] = ($variant['variant_image']) ? $variant['variant_image'] : $variant['image_src'];
        $new_variant['shipping']['requires_shipping'] = $variant['variant_requires_shipping'];
        $new_variant['shipping']['weight_in_grams'] = $variant['variant_grams'] ? $variant['variant_grams'] : 0;
        $new_variant['shipping']['weight_unit'] = $variant['variant_weight_unit'] ? $variant['variant_weight_unit'] : 0;
        $new_variant['inventory']['sku'] = $variant['variant_sku'];
        $new_variant['inventory']['quantity'] = $variant['variant_inventory_qty'];
        $new_variant['inventory']['barcode'] = $variant['variant_barcode'];

        $new_variant['options'] = null;

        if($options){
            foreach ($options as $option) {
                $option_key = $option['key_prefix'];
                $option_value = $variant[$option_key.'_value'];

                $new_variant['options'][$option_key] = $option_value;

                $new_variant['title'] .= ' - '.$option_value;
            }
        }

        return $new_variant;
    }

    public function transformCsvArrayIntoProductsArray($csv_array){
        $products_by_handle = $csv_array->where('variant_price', '!=', "")->groupBy('handle');

        $new_products_object = [];

        foreach ($products_by_handle as $product) {
            $array_key = $product[0]['handle'];

            $new_products_object[$array_key]['title'] = $product[0]['title'];
            $new_products_object[$array_key]['description'] = $product[0]['body_html'];
            $new_products_object[$array_key]['url_title'] = $product[0]['handle'];
            $new_products_object[$array_key]['img_source'] = $product[0]['image_src'];
            $new_products_object[$array_key]['seo_title'] = $product[0]['seo_title'];
            $new_products_object[$array_key]['seo_description'] = $product[0]['seo_description'];

            $options = $this->getCsvArrayOptionsNames($product[0]);

            $new_products_object[$array_key]['options'] = $options;

            foreach ($product as $variant) {
                $new_products_object[$array_key]['variants'][] = $this->generateVariantArray($variant, $options, $product[0]['title']);
            }
        }

        return $new_products_object;
    }

    public function createFromCsvArray(array $csv_array){
        $products = $this->transformCsvArrayIntoProductsArray(collect($csv_array));

        foreach ($products as $product) {
            $this->create($product);
        } 

        return true;
    }

    public function createOptions(int $product_id, array $options){
        $options_ids_by_key_prefix = [];

        foreach ($options as $option) {
            $new_option = new ProductOptions();

            $new_option->product_id = $product_id;
            $new_option->name = $option['name'];

            if($new_option->save()){
                $options_ids_by_key_prefix[] = ['key_prefix' => $option['key_prefix'], 'option_id' => $new_option->id];
            }else{
                throw new CustomException("Erro ao cadastrar uma das opções do produto. Tente novamente em alguns minutos.", 500);
            }
        }

        return $options_ids_by_key_prefix;
    }

    public function createInventory(int $product_variant_id, $inventory){
        $new_inventory = new ProductVariantInventories();

        $new_inventory->product_variant_id = $product_variant_id;
        $new_inventory->sku = $inventory['sku'];
        $new_inventory->barcode = $inventory['barcode'];
        $new_inventory->quantity = $inventory['quantity'];
        $new_inventory->allow_out_of_stock_purchases = 1;

        if($new_inventory->save()){
            return $new_inventory;
        }else{
            throw new CustomException("Erro ao salvar o estoque do produto.", 500);
        }
    }

    public function createOptionsValuesArray(array $options_ids_by_key_prefix, array $variant_options){
        $options_values = [];

        foreach ($options_ids_by_key_prefix as $option_by_key_prefix) {
            $options_values[$option_by_key_prefix['option_id']] = $variant_options[$option_by_key_prefix['key_prefix']];
        }

        return $options_values;
    }

    public function createVariantOptionsValues(int $product_variant_id, $variant_options_values){
        foreach ($variant_options_values as $option_id => $value) {
            $new_option_value = new ProductVariantOptionsValues();

            $new_option_value->product_variant_id = $product_variant_id;
            $new_option_value->product_option_id = $option_id;
            $new_option_value->value = $value;

            if(!$new_option_value->save()){
                throw new CustomException("Erro ao criar uma das variantes em nosso banco de dados. Tente novamente em alguns minutos.", 500);
            };
        }
    }

    public function createVariants(int $product_id, array $product, $options_ids_by_key_prefix){
        $new_variants = [];

        foreach ($product['variants'] as $variant) {
            $new_variant = new ProductVariants();

            $new_variant->product_id = $product_id;
            $new_variant->title = $variant['title'];
            $new_variant->img_source = $variant['img_source'];
            $new_variant->cost = $variant['cost'];
            $new_variant->sku = $variant['inventory']['sku'];
            $new_variant->requires_shipping = ($variant['shipping']['requires_shipping']) ? 1 : 0;
            $new_variant->shipping_cost = 0;
            $new_variant->weight_in_grams = $variant['shipping']['weight_in_grams'];
            $new_variant->weight_unit = $variant['shipping']['weight_unit'];
            $new_variant->published = ($variant['published']) ? 1 : 0;

            if($new_variant->save()){
                /* Creates product variant inventory */
                $this->createInventory($new_variant->id, $variant['inventory']);

                /* Creates product variant options values*/
                if($variant['options'] && $options_ids_by_key_prefix){
                    $variant_options_values = $this->createOptionsValuesArray($options_ids_by_key_prefix, $variant['options']);

                    $this->createVariantOptionsValues($new_variant->id, $variant_options_values);
                }

                $new_variants[] = $new_variant;
            }else{
                throw new CustomException("Erro ao cadastrar uma das variantes. Tente novamente em alguns minutos.", 500);
            }
        }

        return $new_variants;
    }

    public function createProduct(object $product){
        $new_product = new Products();

        $new_product->supplier_id = $this->supplier->id;
        $new_product->title = $product->title;
        $new_product->description = $product->description;
        $new_product->url_title = $product->url_title;
        $new_product->img_source = $product->img_source;
        $new_product->seo_description = $product->seo_description;

        if($new_product->save()){
            return $new_product;
        }else{
            throw new CustomException("Erro ao cadastrar um dos produtos. Tente novamente em alguns minutos.", 500);
        }
    }
}