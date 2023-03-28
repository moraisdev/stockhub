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
                            <h2 class="mb-0">Pedidos enviados</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center">
                        <p>
                            Listagem de pedidos enviados. Você pode marcar o pedido como entregue ou atualizar o código de rastreio a qualquer momento.
                            {{-- Listing orders with pending shipping. You can update the order shipping through the "Update Shipping" button.<br> --}}
                        </p>
                    </div>
                </div>
                <form method="GET" action="{{route('supplier.orders.sent.search')}}">
                    <div class="ml-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">                                
                                    <input class="form-control" type="search" name='query' placeholder='Digite algo para buscar...' id="search-query" value={{isset($query) ? $query : ''}}>
                                </div> 
                            </div>
                            <div class="col-3">                            
                                <button class="btn btn-icon btn-primary" type="submit" id='get-search-button'>
                                    <span class="btn-inner--icon"><i class="fas fa-search"></i></span>
                                    <span class="btn-inner--text">Buscar</span>
                                </button>
                            </div>
                        </div>                    
                    </div>
                    
                    <div class="col-md-12 mb-4">                        
                            <p class='small'>Escolha qual campo buscar</p>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id='filtro-id' value='display_id' name='filter' {{isset($filter) && $filter == 'display_id' ? 'checked' : ''}} {{!isset($filter) ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-id">ID</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id='filtro-id-lojista' value='order_name' name='filter' {{isset($filter) && $filter == 'order_name' ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-id-lojista">ID Lojista</label>
                            </div>
                            {{-- <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id='filtro-loja' value='shop' name='filter' {{isset($filter) && $filter == 'shop' ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-loja">Loja</label>
                            </div> --}}
                            {{-- <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id='filtro-loja' value='shop' name='filter' {{isset($filter) && $filter == 'shop' ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-loja">Nome do Cliente</label>
                            </div> --}}
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id='filtro-data-importacao' value='created_at' name='filter' {{isset($filter) && $filter == 'created_at' ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-data-importacao">Data Importação</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id='filtro-rastreio' value='tracking_number' name='filter' {{isset($filter) && $filter == 'tracking_number' ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-rastreio">Rastreio</label>
                            </div>
                    </div>
                </form>
                @if(isset($countOrdersSearch))
                    <div class='ml-4'>
                        <span class='small'>Resultados: <b>{{$orders->total()}}</b></span>
                    </div>
                @endif
				<div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID lojista</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Produtos</th>
                                <th>Valor total</th>
                                <th>Rastreio</th>
                                <th class="actions-th">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                @php
                                    $get_route = route('supplier.orders.get_json', $order->id);
                                    $form_route = route('supplier.orders.update_shipping', $order->id);
                                    $complete_route = route('supplier.orders.update_shipping', $order->id);
                                    $cancel_route = route('supplier.orders.update_shipping', $order->id);
                                    $receipt_route = route('supplier.orders.upload_receipt', $order->id);
                                    $returned_route = route('supplier.orders.update_returned', $order->id);
                                @endphp
                                <tr>
                                    <td>{{ $order->f_display_id }}</td>
                                    <td>{{ $order->order->name }}</td>
                                    <td>{{ $order->order->customer->first_name.' '.$order->order->customer->last_name }}</td>
                                    <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                                    <td>
                                        <div class="avatar-group">
                                            @foreach($order->items as $item)                                                
                                                @if($item->variant)
                                                    <a href="#" class="avatar avatar-sm" tooltip="true" title="{{ $item->quantity.'x '.$item->variant->title }}">
                                                    <img alt="{{ $item->variant->title }}" src="{{ ($item->variant->img_source) ? $item->variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="rounded-circle bg-white w-100 h-100">
                                                  </a>
                                                @else
                                                    {{-- Busca o produto, mesmo excluído, só a nível de exibição --}}
                                                    @php
                                                     $variant = \App\Models\ProductVariants::withTrashed()->find($item->product_variant_id);
                                                    @endphp
                                                O produto <b>{{$variant->title }}</b> não está mais disponível
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>R$ {{ \App\Http\Controllers\Supplier\FunctionsController::supplierOrderAmount($order) }}</td>
                                    <td>
                                        URL: <a href="{{ $order->shipping->tracking_url }}" target="_blank">{{ $order->shipping->tracking_url }}</a> <br>
                                        Número: {{ $order->shipping->tracking_number }} <br>
                                    </td>
                                    <td>
                                        <a href="{{ route('supplier.orders.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="Detalhes">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                        <a href="#!" class="btn btn-info btn-sm" data-toggle="modal" data-target="#upload-receipt-modal" onclick="uploadReceipt('{{ $receipt_route }}', {{ $order->id }})" tooltip="true" title="Upload de Nota Fiscal">
                                            <i class="fas fa-fw fa-receipt"></i>
                                        </a>
                                        @if ($authenticated_user->shipping_method == 'melhor_envio' && $order->frete_melhor_envio)
                                            <a href="{{$order->frete_melhor_envio->tag_url}}" target="_blank" class="btn btn-danger btn-sm" tooltip="true" title="Imprimir Etiqueta">
                                                <i class="fas fa-fw fa-tag"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('supplier.orders.print_tag', $order->id) }}"  class="btn btn-danger btn-sm" tooltip="true" title="Baixar Etiqueta">
                                                <i class="fas fa-fw fa-tag"></i>
                                            </a>
                                        @endif
                                        <a href="#!" class="btn btn-info btn-sm" data-toggle="modal" data-target="#update-shipping-modal" onclick="updateShipping('{{ $get_route }}', '{{ $form_route }}')" tooltip="true" title="Atualizar código de rastreio">
                                            <i class="fas fa-fw fa-truck"></i>
                                        </a>
                                        <a href="#!" class="btn btn-success btn-sm" data-toggle="modal" data-target="#complete-shipping-modal" onclick="completeShipping('{{ $complete_route }}')" tooltip="true" title="Marcar como entregue">
                                            <i class="fas fa-fw fa-dolly"></i>
                                        </a>
                                        <a href="#!" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#returned-shipping-modal" onclick="returnedShipping('{{ $returned_route }}')" tooltip="true" title="Marcar como Devolvido">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                        <a href="#!" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cancel-shipping-modal" onclick="cancelShipping('{{ $cancel_route }}')" tooltip="true" title="Cancelar envio">
                                            <i class="fas fa-fw fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Não há nenhum pedido pendente para envio.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-block w-100">
                    <div class="float-right mr-2">
                        {!! $orders->appends(request()->query())->render() !!}
                    </div>
                </div>
    		</div>
    	</div>
    </div>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="update-shipping-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="updateShippingForm">
                @csrf
                <input type="hidden" name="status" value="sent">
                <div class="modal-header mt-0">
                    <h5 class="modal-title">Atualizar dados do frete</h5>
                </div>
                <div class="modal-body mb-0">
                    <p>Atualizar dados do frete do pedido. O lojista e o cliente serão notificados quando o código de rastreio for atualizado.</p>
                    <div class="form-group">
                        <label class="control-label">Nome da Transportadora</label>
                        <input type="text" class="form-control" name="company" id="company" placeholder="Nome da transportadora responsável pela entrega." required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">URL de Rastreio</label>
                        <input type="text" class="form-control" name="tracking_url" id="tracking_url" placeholder="Link para onde o cliente usará o código de rastreio ou o link direto para a página de rastreio" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Código de rastreio</label>
                        <input type="text" class="form-control" name="tracking_number" id="tracking_number" placeholder="Digite aqui o código de rastreio do pedido" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-info">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="complete-shipping-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="completeShippingForm">
                @csrf
                <input type="hidden" name="status" value="completed">
                <div class="modal-header mt-0">
                    <h5 class="modal-title">Marcar pedido como entregue</h5>
                </div>
                <div class="modal-body mb-0">
                    <p>Marcar o pedido como entregue. O lojista e o cliente serão notificados desta alteração, o pedido será transferido para a página de pedido completos.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success">Marcar como entregue</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="cancel-shipping-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="cancelShippingForm">
                @csrf
                <input type="hidden" name="status" value="pending">
                <input type="hidden" name="tracking_url" value="">
                <input type="hidden" name="tracking_number" value="">
                <div class="modal-header mt-0">
                    <h5 class="modal-title">Cancelar envio do pedido</h5>
                </div>
                <div class="modal-body mb-0">
                    <p>Você tem certeza que deseja cancelar o envio do pedido? O pedido voltará ao status de pendente e o cliente e lojista serão notificados da alteração.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger">Cancelar envio</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="returned-shipping-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="returnedShippingForm">
                @csrf
                <div class="modal-header mt-0">
                    <h5 class="modal-title">Marcar pedido como devolvido</h5>
                </div>
                <div class="modal-body mb-0">
                    <p>Você tem certeza que deseja marcar o pedido como devolvido? O lojista sera notificado da alteração.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Marcar como devolvido</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var arrOrders = []
    $(document).ready( function () {
        $('input[type=checkbox]').change(function () {
            if($(this).val() === 'select-all'){
                //adiciona marca todos os checkbox como checked
                if($(this).is(":checked")){
                    $('input[type=checkbox]').prop('checked', true)

                    //coloca todos os itens no vetor
                    $('input[type=checkbox]').each(function(){
                        if($(this).val() !== 'select-all'){
                            arrOrders.push($(this).val())
                        }
                    })
                }else{
                    $('input[type=checkbox]').prop('checked', false)
                    arrOrders = []
                }
            }else{
                if($(this).is(":checked")){
                    //adiciona o item no vetor
                    arrOrders.push($(this).val())
                }else{
                    //remove o item do vetor
                    const index = arrOrders.indexOf($(this).val());
                    if (index > -1) {
                      arrOrders.splice(index, 1);
                    }
                }
            }
            //console.log(arrOrders)
        });

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
            "paging":   false,
            "ordering": true,
            "info":     false,
            "searching": false,
            "order": [[ 0, 'desc' ]],
        });
    } );

    function updateShipping(get_route, form_route){
        $.ajax({
            url: get_route,
            method: 'GET',
            success: function(order){
                if(order.shipping){
                    $("#company").val(order.shipping.company);
                    $("#tracking_url").val(order.shipping.tracking_url);
                    $("#tracking_number").val(order.shipping.tracking_number);
                }
            },
            error: function(response){
                //console.log(response);
            }
        })

        $("#updateShippingForm").attr('action', form_route);
    }

    function completeShipping(route){
        $("#completeShippingForm").attr('action', route);
    }

    function cancelShipping(route){
        $("#cancelShippingForm").attr('action', route);
    }

    function returnedShipping(route){
        $("#returnedShippingForm").attr('action', route);
    }
</script>
@endsection
