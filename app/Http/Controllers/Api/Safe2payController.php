<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\ErrorLogs;
use App\Models\OrderGroupPayments;
use App\Models\SupplierOrderGroup;
use App\Models\WebhookCalls;
use App\Services\Shop\OrdersService;
use Illuminate\Http\Request;

class Safe2payController extends Controller
{
    public function transaction(Request $request){
        try {
            WebhookCalls::create([
               'identifier' => 'safe2pay.transaction',
               'content' => json_encode($request->all())
            ]);

            $transaction_id = $request->IdTransaction;

            if($transaction_id){                
                if($request->TransactionStatus['Id'] == 3){ // 3 - Pagamento Autorizado
                    if($request->PaymentMethod['Id'] == 1){ //caso seja boleto
                        $group = SupplierOrderGroup::where('transaction_id', $transaction_id)->first();
                        if($group){
                            if($request->Amount == $request->PaidValue){ //só faz essa verificação caso seja boleto
                                $group->paid_by = 'boleto'; //caso tenha sido pago por boleto
                                OrdersService::paymentReceived($group);
                            }
                        }
                    }
                    
                    if($request->PaymentMethod['Id'] == 6){ //caso seja por pix
                        $group = SupplierOrderGroup::where('transaction_id_pix', $transaction_id)->first();
                        if($group){
                            $group->paid_by = 'pix'; //caso tenha sido pago por pix
                            OrdersService::paymentReceived($group);
                        }
                    }
                }
            }
        } catch(\Exception $e){
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }
}
