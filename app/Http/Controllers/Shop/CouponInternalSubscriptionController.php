<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CouponInternalSubscriptionShop;

class CouponInternalSubscriptionController extends Controller
{
    public function isValid(Request $request){
        $coupon = CouponInternalSubscriptionShop::where('code', $request->coupon)
                                            ->first();

        if($coupon && $request->selectedPlan == 7739){
            if($coupon->code == 'MAWASOCIOFUNDADOR' || $coupon->code == 'dropmakers'){
                $installmentAnnualDiscount = ['R$ 479,40', 'R$ 247,61', 'R$ 165,07', 'R$ 123,81', 'R$ 99,04', 'R$ 82,54', 'R$ 71,09', 'R$ 62,20', 'R$ 55,29', 'R$ 49,76', 'R$ 45,24', 'R$ 41,47'];

                return response()->json([
                    'coupon' => $coupon,
                    'content_selected_plan_text' => '<small><s>R$ 923,70 / Anual</s></small> por R$ 479,40 / Anual',
                    'value' => '<h3>1x de <b>R$ 479,40</b></h3>',
                    'installmentsDiscount' => $installmentAnnualDiscount
                ], 200);
            }
            
            if(strtoupper($coupon->code) == 'DROPNOBR' || strtoupper($coupon->code) == 'DROPWOO'){
                $installmentAnnualDiscount = ['R$ 785,14','R$ 405,53','R$ 270,35','R$ 202,76','R$ 162,21','R$ 135,18','R$ 116,43','R$ 101,87','R$ 90,55','R$ 81,50','R$ 74,09','R$ 67,92'];

                return response()->json([
                    'coupon' => $coupon,
                    'content_selected_plan_text' => '<small><s>R$ 923,70 / Anual</s></small> por R$ 785,14 / Anual',
                    'value' => '<h3>1x de <b>R$ 785,14</b></h3>',
                    'installmentsDiscount' => $installmentAnnualDiscount
                ], 200);
            }
        }

        return response()->json(['msg' => 'Cupon inválido'], 400);
    }
}
