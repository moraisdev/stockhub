@extends('admin.layout.default')

@section('title', 'Detalhes da compra (Lojista)')

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
                                <h2 class="mb-0">Detalhes da compra</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <p>
                                    <b>Origem do pedido:</b> {{ ucfirst($order->external_service) }} <br>
                                    @if($order->external_service == 'shopify')
                                        <b>ID externo:</b> #{{ ucfirst($order->external_id) }} <br>
                                    @endif
                                    <b>Nome do pedido:</b> {{ $order->name }} <br>
                                </p>
                            </div>
                            <div class="col">
                                <p>
                                    <b>Valor na origem:</b> R$ {{ number_format($order->external_price,2,',','.') }} <br>
                                    <b>Valor em produtos:</b> R$ {{ number_format($order->items_amount,2,',','.') }} <br>
                                    @if($order->shipping_amount)
                                        <b>Valor do frete: </b> R$ {{ number_format($order->shipping_amount,2,',','.') }} <br>
                                    @endif
                                    <b>Total do pedido: </b> R$ {{ number_format($order->amount,2,',','.') }} <br>
                                    <b>Status do pagamento aos fornecedores:</b> {{ $order->f_status }} <br>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">Detalhes da entrega</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($order->customer)
                                <div class="col">
                                    <p>
                                        @if($order->external_service == 'shopify')
                                            <b>ID externo do cliente:</b> #{{ $order->customer->external_id }} <br>
                                        @endif
                                        <b>Nome:</b> {{ $order->customer->address->name }} <br>
                                        <b>E-mail:</b> {{ $order->customer->email }} <br>
                                        <b>Telefone:</b> {{ $order->customer->address->phone }} <br>
                                    </p>
                                </div>
                                <div class="col">
                                    <p>
                                        <b>Endereço:</b> {{ $order->customer->address->address1 }} <br>
                                        @if($order->customer->address->address2)
                                            {{ $order->customer->address->address2 }}<br>
                                        @endif
                                        @if($order->customer->address->company)
                                            {{ $order->customer->address->company }}<br>
                                        @endif
                                        <b>CEP:</b> {{ $order->customer->address->zipcode }} <br>
                                        <b>Cidade:</b> {{ $order->customer->address->city }} <br>
                                        <b>Estado:</b> {{ $order->customer->address->province }} <br>
                                    </p>
                                </div>
                            @else
                                <div class="col">
                                    <div class="alert alert-danger">
                                        Não há nenhum cliente ligado à esse pedido e não é possível concluí-lo no momento. Entre em contato com a nossa equipe para resolver este pedido.
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
                                <h2 class="mb-0">Produtos do pedido</h2>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>ID externa</th>
                                <th>SKU</th>
                                <th>Valor externo</th>
                                <th>NCM</th>
                                <th>Variante</th>
                                <th>Valor a pagar</th>
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
                                            <td>{{ $variant->sku }}<br><small>O produto <b>{{$variant->title }}</b> não está mais disponível</small></td>
                                            <td>R$ {{ number_format($item->external_price,2,',','.') }}</td>
                                            <td>{{ $item->ncm }}</td>
                                            <td>{{ $variant->title }}</td>
                                            <td>R$ {{ number_format($variant->price,2,',','.') }}</td>
                                        </tr>
                                    @endif
                                @endif
                                
                            @empty
                                <tr>
                                    <td colspan="6">Nenhum produto ligado à esse pedido.</td>
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
                                <h2 class="mb-0">Pedidos gerados a partir desta compra</h2>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>Fornecedor</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Produtos</th>
                                <th>Tracking number</th>
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
                                                    <br><small>O produto <b>{{$variant->title }}</b> não está mais disponível</small>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        @if($sup_order->shipping && $sup_order->shipping->tracking_number)
                                            {{ $sup_order->shipping->tracking_number }}
                                        @else
                                            Não disponível
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">Nenhum pedido enviado a fornecedores referente à essa compra.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="float-right mt-4">
                    <a href="{{ route('shop.orders.cancel', $order->id) }}" class="btn btn-danger">Cancelar pedido</a>
                </div>
            </div>
        </div>
    </div>
@endsection
