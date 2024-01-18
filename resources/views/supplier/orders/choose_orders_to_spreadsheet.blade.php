@extends('supplier.layout.default')

@section('title', 'Pedidos')

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
                                <h2 class="mb-0">Exportação de pedidos para planilha</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            Escolha os pedidos desejados para gerar uma planilha de exportação. Você pode utilizar os filtros abaixo para ajudar na escolha dos pedidos.
                        </p>
                        <form action="">
                            <div class="row">
                                <div class="col-xl-3 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="pending" {{ request()->status == 'pending' ? 'selected' : '' }}>Pendentes</option>
                                            <option value="sent" {{ request()->status == 'sent' ? 'selected' : '' }}>Enviados</option>
                                            <option value="completed" {{ request()->status == 'completed' ? 'selected' : '' }}>Entregues</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Lojistas</label>
                                        <select name="shop_id" class="form-control" required>
                                            <option value="all">Todos</option>
                                            @foreach($authenticated_user->shops as $shop)
                                                <option value="{{ $shop->id }}" {{ request()->shop_id == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Data inicial</label>
                                        <input type="date" name="start_date" class="form-control datepicker" placeholder="Data inicial" value="{{ request()->start_date }}">
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Data final</label>
                                        <input type="date" name="end_date" class="form-control datepicker" placeholder="Data final" value="{{ request()->end_date }}">
                                    </div>
                                </div>
                                <div class="col-xl-4 offset-lg-4 col-12">
                                    <div class="form-group mb-0">
                                        <button class="btn btn-primary btn-block">Aplicar filtros</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <form method="POST" action="{{ route('supplier.orders.generate_spreadsheet') }}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-flush align-items-center">
                                <thead>
                                <tr>
                                    <th style="width:30px" class="no-sort">
                                        <input type="checkbox" class="select_all">
                                    </th>
                                    <th>ID</th>
                                    <th>ID Lojista</th>
                                    <th>Loja</th>
                                    <th>Data</th>
                                    <th>Produtos</th>
                                    <th>Valor total</th>
                                    <th>Rastreio</th>
                                    <th class="actions-th">Ações</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="order_checkbox" order_id="{{ $order->id }}" name="orders[{{ $order->id }}]">
                                        </td>
                                        <td>{{ $order->f_display_id }}</td>
                                        <td>{{ $order->order->name }}</td>
                                        <td>{{ $order->order->shop->name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                                        <td>
                                            <div class="avatar-group">
                                                @foreach($order->items as $item)
                                                    <a href="#" class="avatar avatar-sm" tooltip="true" title="{{ $item->quantity.'x '.$item->variant->title }}">
                                                        <img alt="{{ $item->variant->title }}" src="{{ ($item->variant->img_source) ? $item->variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="rounded-circle bg-white w-100 h-100">
                                                    </a>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>R$ {{ \App\Http\Controllers\Supplier\FunctionsController::supplierOrderAmount($order) }}</td>
                                        <td>{{ $order->shipping && $order->shipping->tracking_number ? $order->shipping->tracking_number : '' }}</td>
                                        <td>
                                            <a href="{{ route('supplier.orders.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="Detalhes" target="_blank">
                                                <i class="fas fa-fw fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">Não foi encontrado nenhum pedido.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary mb-4 float-right"><i class="fas fa-table"></i> Gerar planilha</button>
                        </div>
                    </form>
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
                "paging": false
            });
        } );

        $('.select_all').change(function(){
            if($(this).is(':checked')){
                $('.order_checkbox').prop('checked', true);
            }else{
                $('.order_checkbox').prop('checked', false);
            }
        });
    </script>
@endsection
