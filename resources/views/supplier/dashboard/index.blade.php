@extends('supplier.layout.default')

@section('title', __('supplier.dashboard_title'))

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
       
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-4 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                @if($authenticated_user->status == 'inactive')
                                    <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __('supplier.account_status') }}</h5>
                                    <span class="h2 font-weight-bold text-danger mb-0">{{ __('supplier.inactive') }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                            <i class="fas fa-times"></i>
                                        </div>
                                    </div>
                                @else
                                    <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __('supplier.account_status') }}</h5>
                                        <span class="h2 font-weight-bold text-success mb-0">{{ __('supplier.active') }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __('supplier.week_earnings') }}</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ __('supplier.dolar') }} {{ number_format($dash_data['week_profit'], 2, ',', '.') }}</span>
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
                <div class="col-xl-4 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __('supplier.total_earnings') }}</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ __('supplier.dolar') }} {{ number_format($dash_data['total_profit'], 2, ',', '.') }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
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
        @if(!$authenticated_user->address || !$authenticated_user->bank || !$authenticated_user->document)) 
            <div class="col-12 mb-2">
                <div class="card shadow bg-danger">
                    <div class="card-body text-white py-2">
                    {{ __('supplier.need_register') }} <a href="{{ route('supplier.profile') }}">{{ __('supplier.here') }}</a> {{ __('supplier.to_update') }}.
                    </div>
                </div>
            </div>
        @else
            @if($authenticated_user->safe2pay_subaccount_id <> null) xor ($authenticated_user->geren_cliente_id <> null ))
                <div class="col-12 mb-2">
                    <div class="card shadow bg-danger">
                        <div class="card-body text-white py-2">
                        teste:
                    {{$authenticated_user->geren_cliente_id}}    
                    {{ __('supplier.linked_safe2pay') }} <a href="https://api.whatsapp.com/send?phone={{ env('SUPPORT_WHATSAPP') }}&text=Ola%2C%20vim%20do%20SAC%20e%20gostaria%20de%20falar%20com%20a%20equipe%20de%20assist%C3%AAncia%20a%20Lojistas" class="text-info" target="_blank">{{ str_replace('55', '', env('SUPPORT_WHATSAPP')) }}</a>.
                        </div>
                    </div>
                </div>
            @endif
        @endif
        @if($authenticated_user->status == 'inactive')
            <div class="col-12 mb-2">
                <div class="card shadow bg-danger">
                    <div class="card-body text-white py-2">
                        {{ __('supplier.paused_activities') }} <a href="{{ route('supplier.profile.toggle_status') }}">{{ __('supplier.here') }}</a> {{ __('supplier.to_restart_activities') }}.
                    </div>
                </div>
            </div>
        @endif
    	<div class="col-12 mb-3">
    		<div class="card shadow">
    			<div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">{{ __('supplier.my_products_page') }}</h2>
                        </div>
                    </div>
                </div>
                @php
                    $slug = str_replace(' ', '_', strtolower($authenticated_user->name));
                @endphp
    			<div class="card-body">
                    {{ __('supplier.send_this_link_to_shopkeeper') }} <span class="text-oragen">{{ __('supplier.show_in_my_products_page') }}</span>. {{ __('supplier.public_and_private_products') }}.
    				{{-- Send this link to a shop to give it access to all products that are checked as <span class="text-orange">Show in my products page</span>. Both public and private products will be available in your products page. --}}
                    <div class="form-group mt-2">
                    <h4>{{ __('supplier.my_products_page') }} {{ __('supplier.click_to_copy') }}</h4>
                        <input type="text" class="form-control" id="link" value="{{ route('shop.partners.products', [$slug, $authenticated_user->private_hash]) }}" readonly>
                        <span class="text-success" id='link_copy_alert' style="display:none; font-size: 0.8rem">
                            {{{ __('supplier.copy_with_success') }}}.
                        </span>
                    </div>
    			</div>
    		</div>
    	</div>
    </div>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card shadow">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase text-muted ls-1 mb-1">{{ __('supplier.10_recent_requests') }}</h6>
                            <h2 class="mb-0">{{ __('supplier.recent_requests') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th>{{ __('supplier.date') }}</th>
                                <th>{{ __('supplier.products') }}</th>
                                <th>{{ __('supplier.total_price') }}</th>
                                <th class="actions-th">{{ __('supplier.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
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
                                    <td>R$ {{ \App\Http\Controllers\Supplier\FunctionsController::supplierOrderAmount($order) }}</td>
                                    <td>
                                        <a href="{{ route('supplier.orders.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="{{ trans('supplier.details') }}">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">{{ __('supplier.dont_have_pending_product') }}</td>
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
<script type="text/javascript">
    $("#link").on('click', function(){
        $("#link").select();
        document.execCommand('copy');

        $('#link_copy_alert').fadeIn();
        window.setTimeout(function(){
            $('#link_copy_alert').fadeOut();
        }, 1000);
    })

</script>
@endsection
