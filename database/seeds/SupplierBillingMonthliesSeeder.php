<?php

use Illuminate\Database\Seeder;
use App\Models\Suppliers;

class SupplierBillingMonthliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //faz um loop para todos os fornecedores
        $suppliers = Suppliers::all();
        foreach ($suppliers as $supplier) {
            $startDate = date('Y-m-d H:i:s');
            DB::table('supplier_billing_monthlies')->insert([
                'start_date' => $startDate, //data de início do cálculo
                'final_date' => date("Y-m-d", strtotime('+30 days', strtotime($startDate))), //data final (data do pagamento da assinatura)
                'supplier_id' => $supplier->id,
                'created_at' => $startDate,
                'updated_at' => $startDate
            ]);
        }
        
    }
}
