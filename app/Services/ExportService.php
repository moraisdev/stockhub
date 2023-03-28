<?php
namespace App\Services;
use App\Models\OrderItems;
use App\Models\SupplierOrders;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Collection;

class ExportService{

    public function ordersToExcel($order_ids){
        if(is_array($order_ids)){
            $orders = SupplierOrders::with(['supplier', 'items', 'order'])->whereIn('id', $order_ids)->get();
            if(count($orders) > 0){
                $parse_orders = $this->parseOrders($orders);
                return $this->generateExcel($parse_orders);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function parseOrders($orders){
        $parse_array = [];
        foreach ($orders as $o){
            $skus='';
            $qtd='';
            $names='';
            $weight = 0;
            $amount = 0;
            foreach($o->items as $index => $i){
                // if($i->quantity > $qtd){
                //     $qtd += $i->quantity - 1;
                // }
                $weight += $i->quantity * $i->variant->weight_in_grams;
                $shop_order_item = OrderItems::where('order_id', $o->order_id)->where('product_variant_id', $i->product_variant_id)->first();
                if($shop_order_item){
                    $amount = $shop_order_item->quantity * $shop_order_item->external_price;
                }else{
                    $amount = 0;
                }
                if($index == 0){
                    $skus.= $i->variant->sku;
                    $qtd.= $i->quantity;
                    $names.=$i->variant->title;
                }else{
                    $skus.= ', '.$i->variant->sku;
                    $qtd.=", ".$i->quantity;
                    $names.=', '.$i->variant->title;
                }
            }

            $parse_array[] = [
                'pedido' => 'F'.$o->display_id,
                'id_lojista' => $o->order->name,
                'pais_destino' => 'BR', //atualmente só fazemos vendas pro Brasil
                'nome_lojista' => $o->order->shop->name,
                'endereco' => $o->order->customer->address->address1.($o->order->customer->address->address2 ? ', '.$o->order->customer->address->address2 : ''),
                'cidade' => $o->order->customer->address->city,
                'estado' => $o->order->customer->address->province_code,
                'cep' => $o->order->customer->address->zipcode,
                'cnpj' => $o->supplier->document,
                'data' => date('d/m/Y H:i:s', strtotime($o->created_at)),                
                'nome' => $o->order->customer->first_name.' '.$o->order->customer->last_name,
                'telefone' => $o->order->customer->address->phone,
                'email' => $o->order->customer->email,
                'cpf' => $o->order->customer->address->company,                
                //'numero' => '',
                'nome_produto' => $names,
                'codigo' => $skus,
                'quantidade' => $qtd,
                'peso' => $weight/1000, //peso kilos, atualmente na mawa se salva em gramas então tem que se realizar uma conversão
                'valor_final' => $amount,
                'cnpj_transportadora' => '',
                'nome_transportadora' => '',                
                'rastreio' => $o->shipping->tracking_number
            ];
        }

        return $parse_array;
    }

    public function generateExcel($array){
        return (new Collection($array))->downloadExcel('pedidos.xlsx', 'Xlsx', true);
    }
}

?>
