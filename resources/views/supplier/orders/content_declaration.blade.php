<!DOCTYPE html>
<html>
<head>
    <title>{{config('app.name')}} - Declaração de Conteúdo do pedido F{{ $supplier_order->display_id }}</title>
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
    </style>
</head>
<body>
    <div style="width: 800px">
        <table class="table table-bordered w-100">
            <thead>
                <tr>
                    <th colspan="4" class="text-center text-uppercase" style="font-size: 18px">Declaração de conteúdo</th>
                </tr>
                <tr>
                    <th style="width: 400px" colspan="2" class="text-center text-uppercase">Remetente</th>
                    <th style="width: 400px" colspan="2" class="text-center text-uppercase">Destinatário</th>
                </tr>
            </thead>
            <tbody>
            @if($supplier->use_shipment_address && $supplier->shipment_address && $supplier->shipment_address->street != null)
                <tr>
                    <td colspan="2"><b>Nome:</b> {{ $supplier_order->order->shop->name }}</td>
                    <td colspan="2"><b>Nome:</b> {{ $supplier_order->order->customer->address->name }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="white-space:pre-wrap"><b>Endereço:</b> {{ $supplier->shipment_address->street }}, {{ $supplier->shipment_address->number }}</td>
                    <td colspan="2" style="white-space:pre-wrap"><b>Endereço:</b> {{ $supplier_order->order->customer->address->address1 }}, {{ $supplier_order->order->customer->address->address2 }}</td>
                </tr>
                <tr>
                    <td><b>Cidade:</b> {{ $supplier->shipment_address->city }}</td>
                    <td><b>UF:</b> {{ $supplier->shipment_address->state_code }}</td>
                    <td><b>Cidade:</b> {{ $supplier_order->order->customer->address->city }}</td>
                    <td><b>UF:</b> {{ $supplier_order->order->customer->address->province_code }}</td>
                </tr>
                <tr>
                    <td><b>CEP:</b> {{ $supplier->shipment_address->zipcode }}</td>
                    <td><b>CPF/CNPJ:</b> {{ $supplier->document }}</td>
                    <td><b>CEP:</b> {{ $supplier_order->order->customer->address->zipcode }}</td>
                    <td><b>CPF/CNPJ:</b> {{ $supplier_order->order->customer->address->company ? $supplier_order->order->customer->address->company : 'Não informado' }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="2"><b>Nome:</b> {{ $supplier_order->order->shop->name }}</td>
                    <td colspan="2"><b>Nome:</b> {{ $supplier_order->order->customer->address->name }}</td>
                </tr>
                <tr>
                    <td colspan="2"><b>Endereço:</b> {{ $supplier_order->order->shop->address->street }}, {{ $supplier_order->order->shop->address->number }}</td>
                    <td colspan="2"><b>Endereço:</b> {{ $supplier_order->order->customer->address->address1 }}, {{ $supplier_order->order->customer->address->address2 }}</td>
                </tr>
                <tr>
                    <td><b>Cidade:</b> {{ $supplier_order->order->shop->address->city }}</td>
                    <td><b>UF:</b> {{ $supplier_order->order->shop->address->state_code }}</td>
                    <td><b>Cidade:</b> {{ $supplier_order->order->customer->address->city }}</td>
                    <td><b>UF:</b> {{ $supplier_order->order->customer->address->province_code }}</td>
                </tr>
                <tr>
                    <td><b>CEP:</b> {{ $supplier_order->order->shop->address->zipcode }}</td>
                    <td><b>CPF/CNPJ:</b> {{ $supplier->document }}</td>
                    <td><b>CEP:</b> {{ $supplier_order->order->customer->address->zipcode }}</td>
                    <td><b>CPF/CNPJ:</b> {{ $supplier_order->order->customer->address->company ? $supplier_order->order->customer->address->company : 'Não informado' }}</td>
                </tr>
            @endif
            </tbody>
        </table>
        <table class="table table-bordered w-100">
            <thead>
                <tr>
                    <th colspan="4" class="text-center text-uppercase" style="font-size: 18px">Identificação dos Bens</th>
                </tr>
                <tr>
                    <th style="width: 100px">Item</th>
                    <th style="width: 450px">Conteúdo</th>
                    <th style="width: 100px">Quant.</th>
                    <th style="width: 150px">Valor</th>
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
                    <td colspan="3" class="text-right py-2"><b>Peso Total (KG)</b></td>
                    <td class="text-right py-2">{{ number_format($total_weight / 1000,2,',','') }}kg</td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered w-100">
            <thead>
                <tr>
                    <th colspan="4" class="text-center text-uppercase" style="font-size: 18px">Declaração</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" style="white-space: normal; text-align: justify;">
                        <p style="font-size: 0.8rem">Declaro que não me enquadro no conceito de contribuinte previsto no art. 4º da Lei Complementar nº 87/1996, uma vez que não realizo,
                        com habitualidade ou em volume que caracterize intuito comercial, operações de circulação de mercadoria, ainda que se iniciem no exterior,
                        ou estou dispensado da emissão da nota fiscal por força da legislação tributária vigente, responsabilizando-me, nos termos da lei e a quem de direito, por informações inverídicas. </p>
                        <p style="font-size: 0.8rem">Declaro ainda que não estou postando conteúdo inflamável, explosivo, causador de combustão espontânea, tóxico, corrosivo, gás ou qualquer outro conteúdo que constitua perigo, conforme o art. 13 da Lei Postal nº 6.538/78.</p>
                        <div class="d-inline float-left" style="margin-top: 20px">
                            ______________________, ________ de __________________ de ____________
                        </div>
                        <div class="d-inline float-right" style="margin-top: 35px">
                            <span style="border-top: 1px solid black; padding: 2px 20px;">Assinatura do Declarante/Remetente</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered w-100">
            <thead>
                <tr>
                    <th colspan="4" class="text-uppercase">Observação:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="py-1" style="white-space: normal; text-align: justify;">
                        <p class="mb-0" style="font-size: 0.7rem">Constitui crime contra a ordem tributária suprimir ou reduzir tributo, ou contribuição social e qualquer acessório (Lei 8.137/90 Art. 1º, V)</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>

<script type="text/javascript">
    window.print();
</script>
