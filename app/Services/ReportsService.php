<?php

namespace App\Services;

/* Models */
use App\Models\Suppliers;
use App\Models\ProductVariants;

use DB;

class ReportsService{

    public $supplier;

    public function __construct(Suppliers $supplier){
        $this->supplier = $supplier;

        if(!$this->supplier){
            throw new \Exception("Supplier not found", 404);
        }
    }

    public function getDashboardStatistics(){
    	$start_date = date('Y-m-d', strtotime('first day of this month'));
    	$final_date = date('Y-m-d', strtotime('last day of this month'));

    	return compact();
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
}