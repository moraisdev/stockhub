@extends('supplier.layout.default')

@section('title', __('supplier.show_partners_tittle'))

@section('content')
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ trans('supplier.dash_data') }}</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
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
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ trans('supplier.dash_data') }}</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ trans('supplier.dash_data') }}</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ trans('supplier.dash_data') }}</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-percent"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <h2 class="mb-0">{{ $shop->name }}</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <p class="mb-0">
                                <b>{{ __('supplier.responsible_name') }}:</b> {{ $shop->responsible_name }} <br>
                                <b>{{ __('supplier.id_documentation') }}:</b> {{ $shop->f_document }} <br>
                                @if(strlen($shop->f_document) > 14)
                                    <b>{{ __('supplier.fantasy_name') }}:</b> {{ $shop->fantasy_name ? $shop->fantasy_name : '{{ trans('supplier.nao_cadastrado') }}' }} <br>
                                    <b>{{ __('supplier.company_name') }}:</b> {{ $shop->corporate_name ? $shop->corporate_name : '{{ trans('supplier.nao_cadastrado') }}' }} <br>
                                    <b>{{ __('supplier.state_registration') }}:</b> {{ $shop->state_registration ? $shop->state_registration : '{{ trans('supplier.nao_cadastrado') }}' }} <br>
                                @endif
                            </p>
                        </div>
                        @if($shop->address)
                        <div class="col">
                            <p class="mb-0">
                                <b>{{ __('supplier.postal_code') }}:</b> {{ $shop->address->zipcode }}<br>
                                <b>{{ __('supplier.adress') }}:</b> {{ $shop->address->street }}, {{ $shop->address->number }}<br>
                                <b>{{ __('supplier.brotherhood') }}:</b> {{ $shop->address->district }}<br>
                                <b>{{ __('supplier.complemment') }}:</b> {{ $shop->address->complement }}<br>
                                <b>{{ __('supplier.city') }}:</b> {{ $shop->address->city }}, {{ $shop->address->state_code }}<br>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">{{ __('supplier.recived_requests_from_shopkeeper') }}</h2>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th>{{ trans('supplier.text_id') }}</th>
                                <th>{{ __('supplier.products') }}</th>
                                <th>{{ __('supplier.total_price') }}</th>
                                <th>{{ trans('supplier.text_status') }}</th>
                                <th>{{ __('supplier.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>
                                        <div class="avatar-group">
                                            @foreach($order->items as $item)
                                                @if($item->variant)
                                                    <a href="#" class="avatar avatar-sm" tooltip="true" title="{{ $item->quantity.'x '.$item->variant->title }}">
                                                      <img alt="{{ $item->variant->title }}" src="{{ ($item->variant->img_source) ? $item->variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="rounded-circle bg-white w-100 h-100">
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>R$ {{ number_format($order->total_amount,2,',','.') }}</td>
                                    <td>{{ ucfirst($order->f_status) }}</td>
                                    <td>
                                        <a href="{{ route('supplier.orders.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="{{ trans('supplier.details') }}">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">{{ __('supplier.dont_having_peding_request_shopkeeper') }}.</td>
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
