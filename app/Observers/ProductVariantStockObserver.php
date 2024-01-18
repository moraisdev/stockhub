<?php

namespace App\Observers;

use App\Models\ProductVariantStock;
use App\Models\ProductVariants;
use App\Models\Products;
use App\Models\Suppliers;
use App\Services\BlingService;
use Illuminate\Support\Facades\File;

class ProductVariantStockObserver
{

    protected $blingService;

    public function __construct(BlingService $blingService)
    {
        $this->blingService = $blingService;
    }

    public function updating(ProductVariantStock $productVariantStock)
    {
    
        if ($productVariantStock->isDirty('quantity') && $productVariantStock->quantity < $productVariantStock->getOriginal('quantity')) {
            
            // Retrieve the product_variant_id from the ProductVariantStock
            $productVariantId = $productVariantStock->product_variant_id;
            
            if ($productVariantId) {
                // Fetch the associated ProductVariants model based on the product_variant_id
                $productVariants = ProductVariants::find($productVariantId);
                
                if ($productVariants) {                    
                    // Fetch the associated Products model using the product_id of the ProductVariants
                    $products = $productVariants->product;
                    
                    if ($products) {                        
                        // Fetch the associated Suppliers model using the supplier_id of the Products
                        $supplier = $products->supplier;
                        
                        if ($supplier) {
                            $xml = $this->xmlStock($productVariants, $productVariantStock, $products);
        
                            // Call the UpdateStockBling method with the necessary data
                            $this->UpdateStockBling($xml, $supplier->bling_apikey, $productVariants->sku);
                        }
                    }
                }
            }
        }
    }
    

    public function UpdateStockBling($xml, $apiKey, $sku)
    {
        try {
    
            // Include $sku in the URL
            $url = "https://bling.com.br/Api/v2/produto/".$sku."/json/";
            $posts = array (
                "apikey" => $apiKey,
                "xml" =>  rawurlencode($xml)
            );
    
            $retorno = json_decode($this->blingService->executeInsertProduct($url, $posts));
            return $retorno;
    
        } catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    public function xmlStock(ProductVariants $productVariants, ProductVariantStock $productVariantStock, Products $products)
    {
        try {

            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<produto>';
            $xml .= '<codigo>'.$productVariants->sku.'</codigo>';
            $xml .= '<descricao>'.$productVariants->title.'</descricao>';
            $xml .= '<descricaoCurta>'.$products->description.'</descricaoCurta>';
            $xml .= '<estoque>'.$productVariantStock->quantity.'</estoque>';
            $xml .= '</produto>';
            
            return $xml;
    
        } catch (\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }
    
}