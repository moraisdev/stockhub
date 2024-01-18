<?php

namespace App\Services;

class PaymentsService{

    public static function generateHash(){
        return bin2hex(random_bytes(16));
    }

    public static function payWithSafeToPay($group, $payment_method){
        $s2p = new SafeToPayService();
        return $s2p->pay($group, $payment_method);
    }


}


