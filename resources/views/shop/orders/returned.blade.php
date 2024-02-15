@extends('shop.layout.default')

@section('content')
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-default"></span>
    </div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">Pedidos Devolvidos</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center">
                            <p>
                                Listagem de pedidos devolvidos.
                                {{-- Listing orders with pending shipping. You can update the order shipping through the "Update Shipping" button.<br> --}}
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Produtos</th>                                
                                <th>Valor total</th>
                                <th>Situação</th>
                                <th>Resolução</th>
                                <th>Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                @php
                                    $route = route('shop.orders.solve_returned', $order->id);
                                @endphp
                                <tr>
                                    @if($order->order->external_service == 'shopify')
                                        <td><a href="https://{{ $authenticated_user->shopify_app->domain }}.myshopify.com/admin/orders/{{ $order->order->external_id }}" target="_blank">{{ $order->order->name }}</a></td>
                                    @endif
                                    <td>{{ date('d/m/Y', strtotime($order->order->created_at)) }}</td>
                                    <td>
                                        <div class="avatar-group">
                                            @foreach($order->order->items as $item)
                                                @php
                                                    $title = ($item->variant) ? $item->variant->title : '?';
                                                @endphp
                                                <a href="#" class="avatar avatar-sm" tooltip="true" title="{{ $item->quantity.'x '.$title }}">
                                                    <img alt="{{ $title }}" src="{{ ($item->variant && $item->variant->img_source) ? $item->variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="rounded-circle bg-white w-100 h-100">
                                                </a>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>R$ {{ number_format($order->order->items_amount, 2, ',', '.') }}</td>
                                    <th>{!!$order->status == 'pending' ? "<span style='color: #f5365c;'>Pendente</span>" : "<span style='color: #2dce89;'>Resolvido</span>" !!}</th>
                                    <th>
                                        {{$order->decision == 'resend' ? 'Reenvio' : '' }}
                                        {{$order->decision == 'credit' ? 'Reembolso' : '' }}
                                    </th>
                                    <td>
                                        @if($order->status == 'pending')
                                            <a href="#!" class="btn btn-success btn-sm" data-toggle="modal" data-target="#solve_returned" onclick="updateReturned('{{ $route }}', '{{number_format($order->order->items_amount, 2, ',', '.')}}')" tooltip="true" title="Detalhes">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('shop.orders.show', $order->order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="Detalhes">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Não há nenhum pedido devolvido.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="solve_returned">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="" id='form_solve_returned'>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Resolver pedido devolvido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>O que deseja fazer com o pedido devolvido?</p>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="opt_order_returned" name="opt_order_returned" value='1' class="custom-control-input" required>
                            <label class="custom-control-label" for="opt_order_returned">Crédito com esse fornecedor no valor de R$ <b><span style='top: 0 !important;' id='returned_value'></span></b></label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="opt_order_returned2" name="opt_order_returned" value='2' class="custom-control-input" required>
                            <label class="custom-control-label" for="opt_order_returned2">Reenviar pedido (uma nova fatura com o valor do frete será gerada)</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                        <button class="btn btn-danger" id='button-mark-selected-sent'>Sim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        function updateReturned(route, value){
            $('#form_solve_returned').attr('action', route)
            $('#returned_value').html(value)
        }

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
                "order": [[ 0, 'desc' ]],
            });
        } );
    </script>
@endsection
