@extends('admin.layout.default')

@section('title', 'Pedidos dos lojistas')

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">

        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card shadow">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">Pedidos pendentes dos Lojistas</h2>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                        <tr>
                            <th>Loja</th>
                            <th>Cliente</th>
                            <th>Fatura</th>
                            <th>Data</th>
                            <th>Ref. Externa</th>
                            <th>Nome do Pedido</th>
                            <th>Valor a pagar</th>
                            <th>Transação Safe2pay</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="text-center">
                                    <a href="{{ route('admin.shops.login', $order->shop->id) }}" target="_blank">{{ $order->shop ? $order->shop->name : '#' }}</a>
                                </td>
                                <td>{{$order->customer->first_name.' '.$order->customer->last_name}}</td>
                                <td>
                                    {{ $order->supplier_order_created == 1 ? 'Gerada' : 'Não gerada' }}
                                </td>
                                <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                                @if($order->external_service == 'shopify')
                                    <td>{{ ucfirst($order->external_service) }}: #{{ $order->external_id }}</td>
                                @endif
                                @if($order->external_service == 'cartx')
                                    <td>{{ ucfirst($order->external_service) }}: #{{ $order->external_id }}</td>
                                @endif
                                <td>{{ $order->name }}</td>
                                <td>R$ {{ number_format($order->amount, 2, ',', '.') }}</td>
                                <td>
                                    @foreach ($order->supplier_orders as $supplier_order)                                        
                                        @php
                                            $supplierOrderGroup = \App\Models\SupplierOrderGroup::find($supplier_order->group_id);

                                            if($supplierOrderGroup && $supplierOrderGroup->transaction_id){
                                                echo 'Boleto: '.$supplierOrderGroup->transaction_id.' ';
                                            }
                                            if($supplierOrderGroup && $supplierOrderGroup->transaction_id_pix){
                                                echo 'Pix: '.$supplierOrderGroup->transaction_id_pix;
                                            }
                                        @endphp
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.shops.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="Detalhes">
                                        <i class="fas fa-fw fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Nenhum pedido pendente de pagamento.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    {!! $orders->render() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
