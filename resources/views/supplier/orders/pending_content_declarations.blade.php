<!DOCTYPE html>
<html>
<head>
    <title>{{config('app.name')}} - {{ trans('supplier.declaracoes_conteudos_pendentes') }}</title>
    <link href="{{ asset('assets/css/argon-dashboard.css?v=1.1.1') }}" rel="stylesheet" />

    <style type="text/css">
        body{
            margin:0;
            padding:10px;
        }

        .w-100{
            width: 100%;
        }

        .table{
            color: black
        }

        table.table-bordered > thead > tr > th{
            border:1px solid #888888;
        }
        table.table-bordered > tbody > tr > td{
            border:1px solid #888888;
        }

        @media print
        {
            .print-break{
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
@foreach($supplier_orders as $supplier_order)
    <div class="print-break">
        <h3>{{ $supplier_order->f_display_id }}</h3>
        <div style="width: 800px">
            <table class="table table-bordered w-100">
                <thead>
                <tr>
                    <th colspan="4" class="text-center text-uppercase" style="font-size: 18px">{{ trans('supplier.declaracao_conteudo_pedido') }}</th>
                </tr>
                <tr>
                    <th style="width: 400px" colspan="2" class="text-center text-uppercase">{{ trans('supplier.remetente') }}</th>
                    <th style="width: 400px" colspan="2" class="text-center text-uppercase">{{ trans('supplier.destinatario') }}</th>
                </tr>
                </thead>
                <tbody>
                @if($supplier->use_shipment_address && $supplier->shipment_address && $supplier->shipment_address->street != null)
                    <tr>
                        <td colspan="2"><b>{{ trans('supplier.name') }}:</b> {{ $supplier_order->order->shop->name }}</td>
                        <td colspan="2"><b>{{ trans('supplier.name') }}:</b> {{ $supplier_order->order->customer->address->name }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="white-space:pre-wrap"><b>{{ trans('supplier.adress') }}:</b> {{ $supplier->shipment_address->street }}, {{ $supplier->shipment_address->number }}</td>
                        <td colspan="2" style="white-space:pre-wrap"><b>{{ trans('supplier.adress') }}:</b> {{ $supplier_order->order->customer->address->address1 }}, {{ $supplier_order->order->customer->address->address2 }}</td>
                    </tr>
                    <tr>
                        <td><b>{{ trans('supplier.city') }}:</b> {{ $supplier->shipment_address->city }}</td>
                        <td><b>{{ trans('supplier.uf') }}</b> {{ $supplier->shipment_address->state_code }}</td>
                        <td><b>{{ trans('supplier.city') }}:</b> {{ $supplier_order->order->customer->address->city }}</td>
                        <td><b>{{ trans('supplier.uf') }}</b> {{ $supplier_order->order->customer->address->province_code }}</td>
                    </tr>
                    <tr>
                        <td><b>{{ trans('supplier.postal_code') }}:</b> {{ $supplier->shipment_address->zipcode }}</td>
                        <td><b>{{ trans('supplier.cpf_cnpj') }}</b> {{ $supplier->document }}</td>
                        <td><b>{{ trans('supplier.postal_code') }}:</b> {{ $supplier_order->order->customer->address->zipcode }}</td>
                        <td><b>{{ trans('supplier.cpf_cnpj') }}</b> {{ $supplier_order->order->customer->address->company ? $supplier_order->order->customer->address->company : '{{ trans('supplier.nao_informado') }}' }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="2"><b>{{ trans('supplier.name') }}:</b> {{ $supplier_order->order->shop->name }}</td>
                        <td colspan="2"><b>{{ trans('supplier.name') }}:</b> {{ $supplier_order->order->customer->address->name }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>{{ trans('supplier.adress') }}:</b> {{ $supplier_order->order->shop->address->street }}, {{ $supplier_order->order->shop->address->number }}</td>
                        <td colspan="2"><b>{{ trans('supplier.adress') }}:</b> {{ $supplier_order->order->customer->address->address1 }}, {{ $supplier_order->order->customer->address->address2 }}</td>
                    </tr>
                    <tr>
                        <td><b>{{ trans('supplier.city') }}:</b> {{ $supplier_order->order->shop->address->city }}</td>
                        <td><b>{{ trans('supplier.uf') }}</b> {{ $supplier_order->order->shop->address->state_code }}</td>
                        <td><b>{{ trans('supplier.city') }}:</b> {{ $supplier_order->order->customer->address->city }}</td>
                        <td><b>{{ trans('supplier.uf') }}</b> {{ $supplier_order->order->customer->address->province_code }}</td>
                    </tr>
                    <tr>
                        <td><b>{{ trans('supplier.postal_code') }}:</b> {{ $supplier_order->order->shop->address->zipcode }}</td>
                        <td><b>{{ trans('supplier.cpf_cnpj') }}</b> {{ $supplier->document }}</td>
                        <td><b>{{ trans('supplier.postal_code') }}:</b> {{ $supplier_order->order->customer->address->zipcode }}</td>
                        <td><b>{{ trans('supplier.cpf_cnpj') }}</b> {{ $supplier_order->order->customer->address->company ? $supplier_order->order->customer->address->company : '{{ trans('supplier.nao_informado') }}' }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
            <table class="table table-bordered w-100">
                <thead>
                <tr>
                    <th colspan="4" class="text-center text-uppercase" style="font-size: 18px">{{ trans('supplier.identificacao_bens') }}</th>
                </tr>
                <tr>
                    <th style="width: 100px">{{ trans('supplier.item') }}</th>
                    <th style="width: 450px">{{ trans('supplier.conteudo') }}</th>
                    <th style="width: 100px">{{ trans('supplier.quantidade') }}</th>
                    <th style="width: 150px">{{ trans('supplier.price') }}</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $products_count = $supplier_order->items->count();
                    $total_amount = 0;
                    $total_weight = 0;

                    if($products_count < 10){
                        $products_count = 10;
                    }
                @endphp
                @for($i = 0; $i <= $products_count; $i++)
                    @if(isset($supplier_order->items[$i]))
                        <tr>
                            <td class="py-2">{{ $i + 1 }}</td>
                            <td class="py-2">{{ $supplier_order->items[$i]->variant->title }}</td>
                            <td class="py-2">{{ $supplier_order->items[$i]->quantity }}</td>
                            @php
                                $order_item = $supplier_order->order->items->where('product_variant_id', $supplier_order->items[$i]->product_variant_id)->first();
                                if($order_item){
                                    $external_price = $order_item->external_price;
                                }else{
                                    $external_price = $supplier_order->items[$i]->amount;
                                }

                                $total_amount += ($external_price * $supplier_order->items[$i]->quantity);
                                $total_weight += ($supplier_order->items[$i]->variant->weight_in_grams * $supplier_order->items[$i]->quantity);
                            @endphp
                            <td class="py-2">R$ {{ number_format($external_price,2,',','.') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif
                @endfor
                <tr>
                    <td colspan="3" class="text-right py-2"><b>Totais</b></td>
                    <td class="text-right py-2">R$ {{ number_format($total_amount,2,',','.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right py-2"><b>{{ trans('supplier.peso_total_kg') }}</b></td>
                    <td class="text-right py-2">{{ number_format($total_weight / 1000,2,',','') }}kg</td>
                </tr>
                </tbody>
            </table>
            <table class="table table-bordered w-100">
                <thead>
                <tr>
                    <th colspan="4" class="text-center text-uppercase" style="font-size: 18px">{{ trans('supplier.declaracao') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="4" style="white-space: normal; text-align: justify;">
                        <p style="font-size: 0.8rem">{{ trans('supplier.text_declaracao') }} </p>
                        <p style="font-size: 0.8rem">{{ trans('supplier.text_declaracao_2') }}</p>
                        <div class="d-inline float-left" style="margin-top: 20px">
                            ______________________, ________ {{ trans('supplier.de') }} __________________ {{ trans('supplier.de') }} ____________
                        </div>
                        <div class="d-inline float-right" style="margin-top: 35px">
                            <span style="border-top: 1px solid black; padding: 2px 20px;">{{ trans('supplier.assinatura_declarante_remetente') }}</span>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="table table-bordered w-100">
                <thead>
                <tr>
                    <th colspan="4" class="text-uppercase">{{ trans('supplier.observacao') }}:</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="4" class="py-1" style="white-space: normal; text-align: justify;">
                        <p class="mb-0" style="font-size: 0.7rem">{{ trans('supplier.text_declaracao_3') }}</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endforeach
</body>
</html>

<script type="text/javascript">
    window.print();
</script>
