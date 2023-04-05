@extends('admin.layout.default')

@section('title', __('supplier.pedidos_title'))

@section('content')
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
                                <h2 class="mb-0">{{ trans('supplier.pedidos_pendentes_fornecedores') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>{{ trans('supplier.fornecedor') }}</th>
                                <th>{{ trans('supplier.lojista') }}</th>
                                <th>{{ trans('supplier.date') }}</th>
                                <th>{{ trans('supplier.products') }}</th>
                                <th>{{ trans('supplier.total_price') }}</th>
                                <th class="actions-th">{{ trans('supplier.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                @php
                                    $route = route('supplier.orders.update_shipping', $order->id);
                                    $cancel_route = route('supplier.orders.cancel', $order->id);
                                    $receipt_route = route('supplier.orders.upload_receipt', $order->id);
                                @endphp
                                <tr>
                                    <td>
                                        @if($order->supplier)
                                            <a href="{{ route('admin.suppliers.login', $order->supplier->id) }}" target="_blank">{{ $order->supplier->name }}</a></td>    
                                        @else
                                            <small>{{ trans('supplier.fornecedor_nao_encontrado') }}</small>
                                        @endif
                                        
                                    <td>{{ $order->order->shop ? $order->order->shop->name : '#' }}</td>
                                    <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                                    <td>
                                        <div class="avatar-group">
                                            @foreach($order->items as $item)
                                                @if($item->variant)
                                                <a href="#" class="avatar avatar-sm" tooltip="true" title="{{ $item->quantity.'x '.$item->variant->title }}">
                                                    <img alt="{{ $item->variant->title }}" src="{{ ($item->variant->img_source) ? $item->variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="rounded-circle bg-white w-100 h-100">
                                                </a>
                                                @else
                                                    @php
                                                        $variant = \App\Models\ProductVariants::withTrashed()->find($item->product_variant_id);
                                                    @endphp

                                                    @if($variant)
                                                    {{ trans('supplier.o_produto') }} <b>{{$variant->title }}</b> {{ trans('supplier.nao_esta_disponivel') }}
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.suppliers.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="{{ trans('supplier.details') }}">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">{{ trans('supplier.text_nao_ha_pedidos_pendentes') }}</td>
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
@endsection
