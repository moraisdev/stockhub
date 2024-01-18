<?php

namespace App\Services;

use App\Models\Suppliers;
use App\Models\SupplierBillingMonthly;
use App\Models\SupplierOrders;

class SupplierPlanService{
    protected $plansPrices;
    protected $supplier;

    public function __construct(Suppliers $supplier){
		$this->supplier = $supplier;
        $this->plansPrices = [
            1000 => 99.90,
            2000 => 99.90,
            3000 => 99.90,
            4000 => 99.90,
            5000 => 99.90,
            6000 => 99.90,
            7000 => 99.90,
            8000 => 99.90,
            9000 => 99.90,
            10000 => 149.90,
            20000 => 249.90,
            30000 => 349.90,
            40000 => 449.90,
            50000 => 549.90,
            60000 => 649.90,
            70000 => 749.90,
            80000 => 849.90,
            90000 => 949.90,
            100000 => 1049.90
        ];
	}

    public function getActualBilling(){
        //calcula o faturamento atual desde a última vez que cobrou
        $lastMonthBilling = SupplierBillingMonthly::where('supplier_id', $this->supplier->id)
                                                ->where('status', 'pending') //isso aqui tem q ser executed, que foi a data do ultimo faturamento
                                                ->orderBy('id', 'desc')
                                                ->first();
        if(!$lastMonthBilling){ //caso não tenha nenhum mes anterior, cria um novo
            $startDate = date('Y-m-d H:i:s');
            $lastMonthBilling = SupplierBillingMonthly::create([
                'start_date' => $startDate, //data de início do cálculo
                'final_date' => date("Y-m-d", strtotime('+30 days', strtotime($startDate))), //data final (data do pagamento da assinatura)
                'supplier_id' => $this->supplier->id
            ]);
        }

        $monthBilling = SupplierOrders::where('supplier_id', $this->supplier->id)
                        ->where('status', 'paid')
                        ->where('created_at', '>=', $lastMonthBilling->start_date)
                        ->where('created_at', '<=', $lastMonthBilling->final_date)
                        ->sum('total_amount');

        //$monthBilling = number_format($monthBilling, 2, ',', '.');
        
        return $monthBilling;
    }

    public function getActualPlanValue(){
        $monthBilling = self::getActualBilling();   

        $indexMin = NULL;

        if($monthBilling >= 1000 && $monthBilling < 2000){ $indexMin = 1000; } //R$ 1.000,00 	 R$ 99,90 	10,0%
        if($monthBilling >= 2000 && $monthBilling < 3000){ $indexMin = 2000; } //R$ 2.000,00 	 R$ 99,90 	5,0%
        if($monthBilling >= 3000 && $monthBilling < 4000){ $indexMin = 3000; } //R$ 3.000,00 	 R$ 99,90 	3,3%
        if($monthBilling >= 4000 && $monthBilling < 5000){ $indexMin = 4000; } //R$ 4.000,00 	 R$ 99,90 	2,5%
        if($monthBilling >= 5000 && $monthBilling < 6000){ $indexMin = 5000; } //R$ 5.000,00 	 R$ 99,90 	2,0%
        if($monthBilling >= 6000 && $monthBilling < 7000){ $indexMin = 6000; } //R$ 6.000,00 	 R$ 99,90 	1,7%
        if($monthBilling >= 7000 && $monthBilling < 8000){ $indexMin = 7000; } //R$ 7.000,00 	 R$ 99,90 	1,4%
        if($monthBilling >= 8000 && $monthBilling < 9000){ $indexMin = 8000; } //R$ 8.000,00 	 R$ 99,90 	1,2%
        if($monthBilling >= 9000 && $monthBilling < 10000){ $indexMin = 9000; } //R$ 9.000,00 	 R$ 99,90 	1,1%
        if($monthBilling >= 10000 && $monthBilling < 20000){ $indexMin = 10000; } //R$ 10.000,00 	 R$ 149,90 	1,5%
        if($monthBilling >= 20000 && $monthBilling < 30000){ $indexMin = 20000; } //R$ 20.000,00 	 R$ 249,90 	1,2%
        if($monthBilling >= 30000 && $monthBilling < 40000){ $indexMin = 30000; } //R$ 30.000,00 	 R$ 349,90 	1,2%
        if($monthBilling >= 40000 && $monthBilling < 50000){ $indexMin = 40000; } //R$ 40.000,00 	 R$ 449,90 	1,1%
        if($monthBilling >= 50000 && $monthBilling < 60000){ $indexMin = 50000; } //R$ 50.000,00 	 R$ 549,90 	1,1%
        if($monthBilling >= 60000 && $monthBilling < 70000){ $indexMin = 60000; } //R$ 60.000,00 	 R$ 649,90 	1,1%
        if($monthBilling >= 70000 && $monthBilling < 80000){ $indexMin = 70000; } //R$ 70.000,00 	 R$ 749,90 	1,1%
        if($monthBilling >= 80000 && $monthBilling < 90000){ $indexMin = 80000; } //R$ 80.000,00 	 R$ 849,90 	1,1%
        if($monthBilling >= 90000 && $monthBilling < 100000){ $indexMin = 90000; } //R$ 90.000,00 	 R$ 949,90 	1,1%
        if($monthBilling >= 100000){ $indexMin = 100000; } //R$ 100.000,00  R$ 1.049,90  1,0%
        
        $nextPlan = $indexMin ? $this->plansPrices[$indexMin] : 0;
        return $nextPlan;
    }

}