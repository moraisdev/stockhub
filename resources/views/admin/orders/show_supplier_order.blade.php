@extends('admin.layout.default')

@section('title', 'Visualizar pedido')

@section('content')
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
                                <h2 class="mb-0">Pedido #{{ $supplier_order->id }}</h2>
                            </div>
                            <div class="d-flex col align-items-center justify-content-end">
                                <h2>Valor total: R$ {{ number_format($supplier_order->total_amount,2,',','.') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>Detalhes do lojista:</h4>
                        <div class="row">
                            <div class="col">
                                <p class="mb-0">
                                    <b>Loja:</b> {{ $supplier_order->order->shop->name }} <br>
                                    <b>Nome do responsável:</b> {{ $supplier_order->order->shop->responsible_name }} <br>
                                </p>
                            </div>
                            <div class="col">
                                <div class="float-right">
                                    <a href="{{ route('admin.shops.login', $supplier_order->order->shop->id) }}" class="btn btn-info" target="_blank">Login no painel do lojista</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">Detalhes do frete</h2>
                            </div>
                            <div class="d-flex col align-items-center justify-content-end">
                                <a href="{{ route('admin.orders.suppliers.print_content_declaration', $supplier_order->id) }}" target="_blank" class="btn btn-danger btn-sm" tooltip="true" title="Imprimir Declaração de Conteúdo">
                                    <i class="fas fa-fw fa-file"></i> Imprimir Declaração de Conteúdo
                                </a>
                                <a href="{{ route('admin.orders.suppliers.print_tag', $supplier_order->id) }}" target="_blank" class="btn btn-danger btn-sm" tooltip="true" title="Imprimir Etiqueta">
                                    <i class="fas fa-fw fa-tag"></i> Imprimir etiqueta
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>Detalhes do cliente:</h4>
                        <div class="row">
                            <div class="col">
                                <p>
                                    <b>Nome:</b> {{ $supplier_order->order->customer->address->name }} <br>
                                    <b>E-mail:</b> {{ $supplier_order->order->customer->email }} <br>
                                    <b>Telefone:</b> {{ $supplier_order->order->customer->address->phone }} <br>
                                </p>
                            </div>
                            <div class="col">
                                <p>
                                    <b>Endereço:</b> {{ $supplier_order->order->customer->address->address1 }} <br>
                                    @if($supplier_order->order->customer->address->address2)
                                        {{ $supplier_order->order->customer->address->address2 }}<br>
                                    @endif
                                    @if($supplier_order->order->customer->address->company)
                                        {{ $supplier_order->order->customer->address->company }}<br>
                                    @endif
                                    <b>CEP:</b> {{ $supplier_order->order->customer->address->zipcode }} <br>
                                    <b>Cidade:</b> {{ $supplier_order->order->customer->address->city }} <br>
                                    <b>Estado:</b> {{ $supplier_order->order->customer->address->province }} <br>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">Meus produtos neste pedido</h2>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>Qtd</th>
                                <th>Produto</th>
                                <th>SKU</th>
                                <th>Valor unitário</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($supplier_order->items as $item)
                                @if($item->variant)
                                    <tr>
                                        <td>{{ $item->quantity }}</td>
                                        <td><a href="{{ route('supplier.products.show', $item->variant->product_id) }}" target="_blank">{{ $item->variant->title }}</a></td>
                                        <td>{{ $item->variant->sku }}</td>
                                        <td>R$ {{ number_format($item->variant->price,2,',','.') }}</td>
                                    </tr>
                                @else
                                    @php
                                        $variant = \App\Models\ProductVariants::withTrashed()->find($item->product_variant_id);
                                    @endphp

                                    @if($variant)
                                        <tr>
                                            <td>{{ $item->quantity }}</td>
                                            <td><a href="{{ route('supplier.products.show', $variant->product_id) }}" target="_blank">{{ $variant->title }}</a></td>
                                            <td>{{ $variant->sku }}<br><small>O produto <b>{{$variant->title }}</b> não está mais disponível</small></td>
                                            <td>R$ {{ number_format($variant->price,2,',','.') }}</td>
                                        </tr>
                                    @endif
                                @endif
                                
                            @empty
                                <tr>
                                    <td colspan="6">Não há nenhum produto ligado à este pedido. Entre em contato com o suporte da {{config('app.name')}}.</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td style="font-size: 1rem" colspan="3" class="text-right">Valor total: </td>
                                <th style="font-size: 1rem">R$ {{ number_format($supplier_order->total_amount,2,',','.') }}</th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
