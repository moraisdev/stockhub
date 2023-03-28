<?php
namespace App\Services;

use App\Mail\ReturnCanceledMail;
use App\Mail\ReturnConfirmedByShopMail;
use App\Mail\ReturnConfirmedBySupplierMail;

use App\Models\Returns;

use Mail;

class ReturnsService{

    public static function confirmByShop($return_id, $shop_id){
        try {
            $return = Returns::whereHas('supplier_order', function($q) use ($shop_id){
                $q->leftJoin('orders', 'supplier_orders.order_id', '=', 'orders.id')->where('orders.shop_id', $shop_id);
            })->find($return_id);

            if(!$return){
                return (object)['success' => false, 'message' => 'Você não tem permissão para acessar essa página.'];
            }

            $return->shop_return_confirmed = 'yes';
            $return->save();

            //Mail::to($return->supplier_order->supplier->email)->send(new ReturnConfirmedByShopMail($return));

            if($return->supplier_return_confirmed == 'yes' && $return->shop_return_confirmed == 'yes'){
                self::markAsResolved($return);

                return (object)['success' => true, 'message' => 'O reembolso foi concluído com sucesso. Ambas as partes confirmaram a conclusão do estorno.'];
            }else{
                return (object)['success' => true, 'message' => 'Você confirmou o reembolso deste pedido. Para a conclusão do estorno o fornecedor também deverá confirmar o reembolso.'];
            }
        } catch(\Exception $e){
            report($e);
            return (object)['success' => false, 'message' => 'Não foi possível confirmar o recebimento do reembolso deste pedido. Tente novamente em alguns minutos'];
        }
    }

    public static function confirmBySupplier($return_id, $supplier_id){
        try {
            $return = Returns::whereHas('supplier_order', function($q) use ($supplier_id){
                $q->where('supplier_id', $supplier_id);
            })->find($return_id);

            if(!$return){
                return (object)['success' => false, 'message' => 'Você não tem permissão para acessar essa página.'];
            }

            $return->supplier_return_confirmed = 'yes';
            $return->save();

            //Mail::to($return->supplier_order->order->shop->email)->send(new ReturnConfirmedBySupplierMail($return));

            if($return->supplier_return_confirmed == 'yes' && $return->shop_return_confirmed == 'yes'){
                self::markAsResolved($return);

                return (object)['success' => true, 'message' => 'O reembolso foi concluído com sucesso. Ambas as partes confirmaram a conclusão do estorno.'];
            }else{
                return (object)['success' => true, 'message' => 'Você confirmou o reembolso deste pedido. Para a conclusão do estorno o lojista também deverá confirmar o reembolso.'];
            }
        } catch(\Exception $e){
            report($e);
            return (object)['success' => false, 'message' => 'Não foi possível confirmar o recebimento do reembolso deste pedido. Tente novamente em alguns minutos'];
        }
    }

    public static function markAsResolved($return){
        $return->status = 'resolved';
        $return->save();
    }

    public static function cancel($return_id, $shop_id){
        try {
            $return = Returns::whereHas('supplier_order', function($q) use ($shop_id){
                $q->leftJoin('orders', 'supplier_orders.order_id', '=', 'orders.id')->where('orders.shop_id', $shop_id);
            })->find($return_id);

            if(!$return){
                return (object)['success' => false, 'message' => 'Você não tem permissão para acessar essa página.'];
            }

            //Mail::to($return->supplier_order->supplier->email)->send(new ReturnCanceledMail($return));

            $return->status = 'canceled';
            $return->save();

            return (object)['success' => true, 'message' => 'Reembolso cancelado com sucesso.'];
        } catch(\Exception $e){
            report($e);
            return (object)['success' => false, 'message' => 'Não foi possível cancelar este reembolso.'];
        }
    }
}
