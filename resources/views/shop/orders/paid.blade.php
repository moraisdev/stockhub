@extends('shop.layout.default')

@section('title', config('app.name').' - Pedidos Pagos')

@section('content')
    <!-- Header -->
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        {{--<div class="container-fluid">
            <div class="header-body">
                <!-- Card stats -->
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Pagos</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ count($orders) }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                            <i class="fas fa-cart-plus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>--}}
    </div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">{{ trans('supplier.pedidos_pagos') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center">
                            <p>
                                Listagem de pedidos pagos ao fornecedor. Você pode visualizar os detalhes do pedido para saber quais fornecedores já enviaram os produtos e quais estão pendentes de envio, assim como consultar o código de rastreio.<br>
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>{{ trans('supplier.date') }}</th>
                                <th>{{ trans('supplier.ref_externa') }}</th>
                                <th>{{ trans('supplier.nome_pedido') }}</th>
                                <th>{{ trans('supplier.valor_pagar') }}</th>
                                <th>{{ trans('supplier.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                @php($receipt_route = route('shop.orders.upload_receipt', $order->id))
                                <tr>
                                    <td><span class="d-none">{{ date('Y-m-d', strtotime($order->created_at)) }}</span>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                                    @if($order->external_service == 'shopify')
                                        <td>{{ ucfirst($order->external_service) }}: <a href="https://{{ $authenticated_user->shopify_app->domain }}.myshopify.com/admin/orders/{{ $order->external_id }}" target="_blank">#{{ $order->external_id }}</a></td>
                                    @endif
                                    <td>{{ $order->name }}</td>
                                    <td>R$ {{ number_format($order->amount, 2, ',', '.') }}</td>
                                    <td>
                                        @if($order->supplier_order)
                                        <a href="#!" class="btn btn-info btn-sm" data-toggle="modal" data-target="#upload-receipt-modal" onclick="uploadReceipt('{{ $receipt_route }}', {{ $order->id }})" tooltip="true" title="{{ trans('supplier.notas_fiscais_title') }}">
                                            <i class="fas fa-fw fa-receipt"></i>
                                        </a>
                                        @endif
                                        <a href="{{ route('shop.orders.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="{{ trans('supplier.details') }}">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Nenhum pedido pago ao fornecedor.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready( function () {
            $('table').DataTable({
                language: {
                    "sProcessing":   "A processar...",
                    "sLengthMenu":   "Mostrar _MENU_ registros",
                    "sZeroRecords":  "Não foram encontrados resultados",
                    "sInfo":         "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "sInfoEmpty":    "Mostrando de 0 até 0 de 0 registros",
                    "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
                    "sInfoPostFix":  "",
                    "sSearch":       "Procurar:",
                    "sUrl":          "",
                    "oPaginate": {
                        "sFirst":    ">>",
                        "sPrevious": "<",
                        "sNext":     ">",
                        "sLast":     "<<"
                    }
                },
                "columnDefs": [{ targets: 'no-sort', orderable: false }],
                "order": [[ 1, 'desc' ]],
            });
        } );
    </script>
@endsection
