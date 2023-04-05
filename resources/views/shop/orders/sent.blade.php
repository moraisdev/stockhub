@extends('shop.layout.default')

@section('title', config('app.name').' - Pedidos Pagos')

@section('content')
    <!-- Header -->
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        {{--<div class="container-fluid">
            <div class="header-body">
                <!-- Card stats -->
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Pagos</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ count($orders) }}</span>
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
                </div>
            </div>
        </div>--}}
    </div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">{{ trans('supplier.pedidos_enviados') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center">
                            <p>
                                Listagem de pedidos enviados pelo fornecedor.<br>
                            </p>
                        </div>
                    </div>
                    <form method="GET" action="{{route('shop.orders.sent.search')}}">
                        <div class="ml-4">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">                                
                                        <input class="form-control" type="search" name='query' placeholder="{{ trans('supplier.digite_para_buscar') }}" id="search-query" value='{{isset($query) ? $query : ''}}'>
                                    </div> 
                                </div>
                                <div class="col-3">                            
                                    <button class="btn btn-icon btn-primary" type="submit" id='get-search-button'>
                                        <span class="btn-inner--icon"><i class="fas fa-search"></i></span>
                                        <span class="btn-inner--text">{{ trans('supplier.buscar') }}</span>
                                    </button>
                                </div>
                            </div>                    
                        </div>
                        
                        <div class="col-md-12 mb-4">                        
                                <p class='small'>{{ trans('supplier.escolha_qual_campo_buscar') }}</p>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id='filtro-cliente' value='customer' name='filter' {{isset($filter) && $filter == 'customer' ? 'checked' : ''}} {{!isset($filter) ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="filtro-cliente">{{ trans('supplier.text_client') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id='filtro-id' value='name' name='filter' {{isset($filter) && $filter == 'name' ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="filtro-id">{{ trans('supplier.text_id') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id='filtro-data-importacao' value='created_at' name='filter' {{isset($filter) && $filter == 'created_at' ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="filtro-data-importacao">{{ trans('supplier.data_importacao') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id='filtro-data-origem' value='external_created_at' name='filter' {{isset($filter) && $filter == 'external_created_at' ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="filtro-data-origem">Data na Origem</label>
                                </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>{{ trans('supplier.data_importacao') }}</th>
                                <th>Data na Origem</th>
                                <th>{{ trans('supplier.price') }}</th>
                                <th>{{ trans('supplier.text_client') }}</th>
                                <th>{{ trans('supplier.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                @php($receipt_route = route('shop.orders.upload_receipt', $order->id))
                                <tr>
                                    
                                    @if($order->external_service == 'shopify')
                                        <td><a href="https://{{ $authenticated_user->shopify_app->domain }}.myshopify.com/admin/orders/{{ $order->external_id }}" target="_blank">{{ $order->name }}</a></td>
                                    @else 
                                    <td>{{ $order->id }}</a></td>
                                    
                                    @endif
                                    <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                                    @if ($order->external_created_at != null)
                                    <td>{{ date('d/m/Y', strtotime($order->external_created_at)) }}</td>
                                    @else
                                    <td>-</td>
                                    @endif
                                    <td>R$ {{ number_format($order->amount, 2, ',', '.') }}</td>
                                    <td class="text-gray">
                                        - {{ $order->customer->first_name.' '.$order->customer->last_name }}<br>
                                        - {{ $order->customer->email }}<br>
                                        - {{ $order->customer->address->phone }}<br>
                                        - {{ $order->customer->address->address1.', '.$order->customer->address->address2.'-'.$order->customer->address->city.'/'.$order->customer->address->privince_code.'-'.$order->customer->address->zipcode }}
                                    </td>
                                    <td>
                                        @if($order->supplier_order)
                                            <a href="#!" class="btn btn-info btn-sm" data-toggle="modal" data-target="#upload-receipt-modal" onclick="uploadReceipt('{{ $receipt_route }}', {{ $order->id }})" tooltip="true" title="{{ trans('supplier.notas_fiscais_title') }}">
                                                <i class="fas fa-fw fa-receipt"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('shop.orders.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="{{ trans('supplier.details') }}">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Nenhum pedido pago ao fornecedor.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {!! $orders->appends(request()->query())->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready( function () {
            // $('table').DataTable({
            //     language: {
            //         "sProcessing":   "A processar...",
            //         "sLengthMenu":   "Mostrar _MENU_ registros",
            //         "sZeroRecords":  "Não foram encontrados resultados",
            //         "sInfo":         "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            //         "sInfoEmpty":    "Mostrando de 0 até 0 de 0 registros",
            //         "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
            //         "sInfoPostFix":  "",
            //         "sSearch":       "Procurar:",
            //         "sUrl":          "",
            //         "oPaginate": {
            //             "sFirst":    ">>",
            //             "sPrevious": "<",
            //             "sNext":     ">",
            //             "sLast":     "<<"
            //         }
            //     },
            //     "columnDefs": [{ targets: 'no-sort', orderable: false }],
            //     "order": [[ 0, 'desc' ]],
            // });
        } );
    </script>
@endsection
