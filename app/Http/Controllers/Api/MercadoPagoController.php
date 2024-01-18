<?php
namespace App\Http\Controllers\Api;

use App\Models\OrderGroupPayments;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

use DB;
use MPSdk;
use MPPreference;
use MPItem;
use MPPayment;
use MPMerchantOrder;

class MercadoPagoController extends Controller
{
    public function payment_webhook($hash, Request $request){
        $payment = OrderGroupPayments::where('hash', $hash)->first();

        $retorno;

        if($payment){
            $retorno[] = 'Payment ok';
            if($payment->supplier && $payment->supplier->mp_account && $payment->supplier->mp_account->access_token){
                $retorno[] = 'Access token ok';
                MpSDK::setAccessToken($payment->supplier->mp_account->access_token);

                $merchant_order = null;
                if(isset($_GET["topic"])) {
                    $retorno[] = 'Topic ok';
                    switch ($_GET["topic"]) {
                        case "payment":
                            $payment = MPPayment::find_by_id($_GET["id"]);
                            $merchant_order = MPMerchantOrder::find_by_id($payment->order->id);
                            break;
                        case "merchant_order":
                            $merchant_order = MPMerchantOrder::find_by_id($_GET["id"]);
                            break;
                    }

                    $paid_amount = 0;
                    if ($merchant_order) {
                        $retorno[] = 'Merchant order ok';
                        $retorno[] = json_encode($merchant_order);
                        foreach ($merchant_order->payments as $payment) {
                            if ($payment->status == 'approved') {
                                $paid_amount += $payment->transaction_amount;
                            }
                        }

                        $retorno[] = $paid_amount;

                        //if ($paid_amount >= $merchant_order->total_amount) {
                            $retorno[] = 'Paid amount ok';
                            $external_reference = explode('-', $merchant_order->external_reference);
                            if($payment->id == $external_reference[1]) {
                                $retorno[] = 'Id pagamento e external reference ok';
                                $payment->group->orders->each(function ($sup_order){
                                    $sup_order->update(['status' => 'paid']);
                                    $sup_order->order->update(['status' => 'paid']);
                                });
                                $payment->group->update(['status' => 'paid']);
                                $payment->update(['status' => 'paid']);
                            }
                        //}
                    }
                }
            }
        }

        return $retorno;
    }

}
