<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\WebhookCalls;
use App\Models\ProductVariants;
use App\Models\ProductVariantStock;
use Illuminate\Support\Facades\Log;
use App\Models\SupplierOrderGroup;
use App\Models\SupplierOrders;

class GerencianetController extends Controller
{

    public function webhookboleto(Request $request){
      
        try {
            $response = json_encode($request->data);
            
            $charge = $response['identifiers']['charge_id'];

            $order = SupplierOrderGroup::where('transaction_id', $charge)->first();
           
            $order->status = ['status']['current'];
            

            if($order->save()){
                return response()->json([
                    'success' => 'Atualizado com sucesso.'
                ], 200); 
            }else{
                return response()->json([
                    'error' => 'Erro ao atualizar.'
                ], 400); 
            }

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar pagamento');
            Log::error($e);
        }
   

  
    }




}


