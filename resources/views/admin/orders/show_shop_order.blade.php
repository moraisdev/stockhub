@extends('admin.layout.default')

@section('title', __('supplier.detalhes_compra_lojista'))

@section('content')
    <!-- Header -->
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <!-- Card stats -->
                <div class="row">

                </div>
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
                                <h2 class="mb-0">{{ trans('supplier.detalhes_compra') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <p>
                                    <b>{{ trans('supplier.origem_pedido') }}:</b> {{ ucfirst($order->external_service) }} <br>
                                    @if($order->external_service == 'shopify')
                                        <b>{{ trans('supplier.id_externo') }}:</b> #{{ ucfirst($order->external_id) }} <br>
                                    @endif
                                    <b>{{ trans('supplier.nome_pedido') }}:</b> {{ $order->name }} <br>
                                </p>
                            </div>
                            <div class="col">
                                <p>
                                    <b>{{ trans('supplier.valor_origem') }}:</b> R$ {{ number_format($order->external_price,2,',','.') }} <br>
                                    <b>{{ trans('supplier.valor_produtos') }}:</b> R$ {{ number_format($order->items_amount,2,',','.') }} <br>
                                    @if($order->shipping_amount)
                                        <b>{{ trans('supplier.valor_frete') }}: </b> R$ {{ number_format($order->shipping_amount,2,',','.') }} <br>
                                    @endif
                                    <b>{{ trans('supplier.total_pedido') }}: </b> R$ {{ number_format($order->amount,2,',','.') }} <br>
                                    <b>{{ trans('supplier.status_pagamento_fornecedores') }}:</b> {{ $order->f_status }} <br>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">{{ trans('supplier.detalhes_entrega') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($order->customer)
                                <div class="col">
                                    <p>
                                        @if($order->external_service == 'shopify')
                                            <b>{{ trans('supplier.id_cliente_externo') }}:</b> #{{ $order->customer->external_id }} <br>
                                        @endif
                                        <b>{{ trans('supplier.name') }}:</b> {{ $order->customer->address->name }} <br>
                                        <b>{{ trans('supplier.text_email') }}:</b> {{ $order->customer->email }} <br>
                                        <b>{{ trans('supplier.text_phone') }}:</b> {{ $order->customer->address->phone }} <br>
                                    </p>
                                </div>
                                <div class="col">
                                    <p>
                                        <b>{{ trans('supplier.adress') }}:</b> {{ $order->customer->address->address1 }} <br>
                                        @if($order->customer->address->address2)
                                            {{ $order->customer->address->address2 }}<br>
                                        @endif
                                        @if($order->customer->address->company)
                                            {{ $order->customer->address->company }}<br>
                                        @endif
                                        <b>{{ trans('supplier.postal_code') }}:</b> {{ $order->customer->address->zipcode }} <br>
                                        <b>{{ trans('supplier.city') }}:</b> {{ $order->customer->address->city }} <br>
                                        <b>{{ trans('supplier.estado') }}:</b> {{ $order->customer->address->province }} <br>
                                    </p>
                                </div>
                            @else
                                <div class="col">
                                    <div class="alert alert-danger">
                                    {{ trans('supplier.cliente_nao_ligado_pedido') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">{{ trans('supplier.produtos_pedido') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>{{ trans('supplier.id_externo') }}</th>
                                <th>{{ trans('supplier.sku') }}</th>
                                <th>{{ trans('supplier.valor_externo') }}</th>
                                <th>{{ trans('supplier.ncm') }}</th>
                                <th>{{ trans('supplier.variante') }}</th>
                                <th>{{ trans('supplier.valor_pagar') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($order->items as $item)
                                @if($item->variant)
                                    <tr>
                                        <td>#{{ $item->external_variant_id }}</td>
                                        <td>{{ $item->sku }}</td>
                                        <td>R$ {{ number_format($item->external_price,2,',','.') }}</td>
                                        <td>{{ $item->ncm }}</td>
                                        @if($item->variant)
                                            <td>{{ $item->variant->title }}</td>
                                            <td>R$ {{ number_format($item->variant->price,2,',','.') }}</td>
                                        @else
                                            <td>#</td>
                                            <td>R$ 0,00</td>
                                        @endif
                                    </tr>
                                @else
                                    @php
                                        $variant = \App\Models\ProductVariants::withTrashed()->find($item->product_variant_id);
                                    @endphp

                                    @if($variant)
                                        <tr>
                                            <td>#{{ $item->external_variant_id }}</td>
                                            <td>{{ $variant->sku }}<br><small>{{ trans('supplier.o_produto') }} <b>{{$variant->title }}</b> {{ trans('supplier.nao_esta_disponivel') }}</small></td>
                                            <td>R$ {{ number_format($item->external_price,2,',','.') }}</td>
                                            <td>{{ $item->ncm }}</td>
                                            <td>{{ $variant->title }}</td>
                                            <td>R$ {{ number_format($variant->price,2,',','.') }}</td>
                                        </tr>
                                    @endif
                                @endif
                                
                            @empty
                                <tr>
                                    <td colspan="6">{{ trans('supplier.nenhum_produto_ligado') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">{{ trans('supplier.pedidos_gerados_desta_compra') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>{{ trans('supplier.fornecedor') }}</th>
                                <th>{{ trans('supplier.total') }}</th>
                                <th>{{ trans('supplier.text_status') }}</th>
                                <th>{{ trans('supplier.products') }}</th>
                                <th>{{ trans('supplier.tracking_number') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($order->supplier_orders as $sup_order)
                                <tr>
                                    <td>{{ $sup_order->supplier->name }}</td>
                                    <td>{{ number_format($sup_order->total_amount, 2, ',', '') }}</td>
                                    <td>{{ $sup_order->status }}</td>
                                    <td>
                                        <div class="avatar-group">
                                            @foreach($sup_order->items as $item)
                                                @if($item->variant)
                                                <a href="#" class="avatar avatar-sm" tooltip="true" title="{{ $item->quantity.'x '.$item->variant->title }}">
                                                    <img alt="{{ $item->variant->title }}" src="{{ ($item->variant->img_source) ? $item->variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="rounded-circle bg-white w-100 h-100">
                                                </a>
                                                @else
                                                    @php
                                                        $variant = \App\Models\ProductVariants::withTrashed()->find($item->product_variant_id);
                                                    @endphp
                                                    @if($variant)
                                                    <a href="#" class="avatar avatar-sm" tooltip="true" title="{{ $item->quantity.'x '.$variant->title }}">
                                                        <img alt="{{ $variant->title }}" src="{{ ($variant->img_source) ? $variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="rounded-circle bg-white w-100 h-100">
                                                    </a>
                                                    <br><small>{{ trans('supplier.o_produto') }} <b>{{$variant->title }}</b> {{ trans('supplier.nao_esta_disponivel') }}</small>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        @if($sup_order->shipping && $sup_order->shipping->tracking_number)
                                            {{ $sup_order->shipping->tracking_number }}
                                        @else
                                        {{ trans('supplier.nao_disponivel') }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">{{ trans('supplier.nenhum_pedido_enviado_fornecedores_compra') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="float-right mt-4">
                    <a href="{{ route('shop.orders.cancel', $order->id) }}" class="btn btn-danger">{{ trans('supplier.cancelar_pedido') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
