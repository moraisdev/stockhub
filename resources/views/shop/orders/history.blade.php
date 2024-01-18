@extends('shop.layout.default')

@section('title', config('app.name').' - Histórico de pedidos')

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
                            <h2 class="mb-0">Pedidos já enviados aos fornecedores</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center">
                        <p>
                            Listagem de pedidos que foram pagos ao fornecedor. Nessa página você pode fazer o acompanhamento do status do frete do pedido.<br>
                        </p>
                    </div>
                </div>
				<div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th style="width:30px"></th>
                                <th>Data de pagamento</th>
                                <th>Ref. Externa</th>
                                <th>Nome do Pedido</th>
                                <th>Valor pago</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="orders[{{ $order->id }}]">
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                                    @if($order->external_service == 'shopify')
                                    <td>{{ ucfirst($order->external_service) }}: <a href="https://{{ $authenticated_user->shopify_app->domain }}.myshopify.com/admin/orders/{{ $order->external_id }}" target="_blank">#{{ $order->external_id }}</a></td>
                                    @endif
                                    <td>{{ $order->name }}</td>
                                    <td>R$ {{ number_format($order->amount, 2, ',', '.') }}</td>
                                    <td>Envio pendente</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Nenhum pedido importado.</td>
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
