<?php

namespace App\Services\Shop;

use App\Exceptions\CustomException;

use Illuminate\Http\Request;

/* Models */
use App\Models\Shops;
use App\Models\Suppliers;

use App\Models\Products;
use App\Models\ProductVariants;

use App\Models\ShopProducts;

use App\Services\PartnersService;

class ProductsService{

    public $shop, $suppliers;

    public function __construct(Shops $shop){
        $this->shop = $shop;

        if(!$this->shop){
            throw new CustomException("Aconteceu um erro inesperado. Tente novamente em alguns minutos.", 404);
        }
    }

    public function getProductsStatistics(){
        $start_date = date('Y-m-d', strtotime('first day of this month'));
        $final_date = date('Y-m-d', strtotime('last day of this month'));

        $sold_products_count = $this->getSoldProductsCount($start_date, $final_date);
        $most_sold_variant = $this->getMostSoldVariant($start_date, $final_date);

        return compact('sold_products_count', 'most_sold_variant');
    }

    protected function getSoldProductsCount($start_date, $final_date){
        return 0;
    }

    protected function getMostSoldVariant($start_date, $final_date){
        return 0;
    }

    public function get(){
        return Products::whereIn('id', $this->shop->products->pluck('id'))->get();
    }

    public function getByVariantId($variant_id){
        $variant = ProductVariants::find($variant_id);

        $product = Products::whereIn('id', $this->shop->products->pluck('id'))->find($variant->product_id);

        if(!$product){
            throw new CustomException("Produto não encontrado.", 404);
        }

        return $product;
    }

    public function paginate(int $quantity){
        return Products::whereIn('id', $this->shop->products->pluck('id'))->paginate($quantity);
    }

    public function find(int $id){
        $product = Products::whereIn('id', $this->shop->products->pluck('id'))->with(['variants', 'options'])->find($id);

        if(!$product){
            throw new CustomException("Produto não encontrado.", 404);
        }

        return $product;
    }

    public function findByHash(string $hash){
        $product = Products::with(['variants', 'options'])->where('hash', $hash)->first();

        if(!$product){
            throw new CustomException("Produto não encontrado.", 404);
        }

        return $product;
    }

    public function getSupplierProductsPage(Suppliers $supplier){
        if(!$supplier){
            throw new CustomException("Fornecedor inválido.", 404);
        }

        if($this->shop->products){
            $products_ids = $this->shop->products->pluck('id')->toArray();
        }else{
            $products_ids = [];
        }

        $products = Products::with(['variants', 'options'])->where('show_in_products_page', 1)->where('supplier_id', $supplier->id)->whereNotIn('id', $products_ids)->get();

        return $products;
    }

    public function link(Products $product){
        $link = ShopProducts::firstOrNew(['shop_id' => $this->shop->id, 'product_id' => $product->id]);

        if($link->id == null){
            $link->date = date('Y-m-d');
            $link->exported = 0;
        }

        if(!$link->save()){
            return false;
        }

        PartnersService::link($this->shop->id, $product->supplier_id);
        
        return true;
    }
}