@extends('shop.layout.default')

@section('title', config('app.name').' - Pagar pedidos')

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
                            <div class="col-12">
                                <h2 class="mb-0">Pagar pedido</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                            {{ trans('supplier.price') }}: <h3>R$ {{ number_format($payment->amount, 2, ',', '.') }}</h3>
                            </div>
                            <div class="col-lg-4">
                                Você está pagando para: <h3>{{ $supplier->name }}</h3>
                            </div>
                            <div class="col-lg-4">
                                Gateway selecionado: <h3>{{ $gateway->name }}</h3>
                            </div>
                        </div>
                        @if($gateway->identifier == 'mercado_pago')
                            <div class="row">
                                <div class="col">
                                    <form action="{{ route('shop.orders.groups', $group->id) }}" method="GET" class="mt-3 text-right">
                                        {{ csrf_field() }}
                                        <script src="https://www.mercadopago.com.br/integrations/v1/web-payment-checkout.js" data-preference-id="{{ $preference->id }}"></script>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
