@extends('supplier.layout.default')

@section('title', 'Pedidos')

@section('content')
<style>
    /* .dt-button{
        border: none;
        position: relative;
        text-transform: none;
        transition: all 0.15s ease;
        letter-spacing: 0.025em;
        font-size: 0.875rem;
        will-change: transform;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.375rem;
        color: #525f7f;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        display: inline-block;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        border: 1px solid transparent;
        margin-left: 25px;
    } */

    #formRastreioBling, #formRastreioChinaDivision{
        display: inline-block;
    }

    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_processing, .dataTables_wrapper .dataTables_paginate{
        color: #525f7f !important;
    }

    table.dataTable.no-footer{
        border-bottom: 1px solid #e9ecef !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled{
        color: #8898aa;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dee2e6 !important;
        border-radius: 50% !important;
    }

    .paginate_button{
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        margin: 0 3px;
        border-radius: 50% !important;
        width: 36px;
        height: 36px;
        font-size: 0.875rem;
        border-color: #dee2e6 !important;
        color: #8898aa !important;
    }

    .paginate_button:hover{
        z-index: 2 !important;
        color: #8898aa !important;
        text-decoration: none !important;
        background-color: #dee2e6 !important;
        border-color: #dee2e6 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
        z-index: 1;
        color: #fff !important;
        background-color: #02A0FC !important;
        border-color: #02A0FC !important;
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08) !important;
        background: #02A0FC !important;
    }
</style>
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
                            <h2 class="mb-0">Pedidos pendentes</h2>
                        </div>
                        <div class="col">
                            <a href="{{ route('supplier.orders.choose_orders_to_spreadsheet') }}" class="btn btn-success float-right btn-sm"><i class="fas fa-table"></i> Exportar para planilha</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center">
                        <p>
                            Listagem de pedidos com frete pendente. Você pode marcar o pedido como enviado e atualizar o código de rastreio a qualquer momento.
                            {{-- Listing orders with pending shipping. You can update the order shipping through the "Update Shipping" button.<br> --}}
                        </p>
                    </div>
                    <div class="row justify-content-end">
                        @if ($authenticated_user->shipping_method == 'melhor_envio')
                            <div class="row d-flex pr-3" style='flex-direction: column;'>
                                <form action="{{route('supplier.orders.print_pending_tags_melhor_envio')}}" method='POST' target="_blank" class='mr-2' id='form-print-tags-melhor-envio' >
                                    @csrf
                                    <input type="hidden" name="print_tags_melhor_envio" id='print_tags_melhor_envio'>
                                </form>
                                <a id='btn-print-tags-melhor-envio' href='javascript:void(0);' class="btn btn-sm btn-danger mb-3" style='background-color: #3c5163 !important; border-color: #3c5163 !important;'>
                                    Imprimir Etiquetas
                                    <img src="{{asset('img/icons/logo-melhor-envio.png')}}" alt="" srcset="" style='height: 25px;'>
                                </a>
                                <small><a href="https://youtu.be/LqtCO4Y4UWM" target='_blank'>Veja como imprimir várias etiquetas</a></small>
                            </div>
                        @endif
                        
                        {{-- <a href="{{ route('supplier.orders.print_pending_tags') }}" target="_blank" class="btn btn-sm btn-danger">Imprimir Etiquetas</a>
                        <a href="{{ route('supplier.orders.print_pending_content_declaration') }}" target="_blank" class="btn btn-sm btn-danger">Imprimir Declarações</a> --}}
                    </div>
                </div>

				<div class="table-responsive">
                    <table class="table table-flush align-items-center display">
                        <thead>
                            <tr>
                                
                                <th>ID</th>
                                <th>ID Lojista/Cliente</th>
                                <th>Produtos</th>
                                <th>Valor total/Data</th>
                                <th>Rastreio/Status</th>
                                <th class="actions-th">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $arrMsgMelhorEnvio = ['pending' => 'Pendente Envio', 'released' => 'Liberado', 'posted' => 'Postado', 'sent' => 'Enviado', 'canceled' => 'Cancelado', 'delivered' => 'Entregue'];

                            @endphp
                            @forelse($orders as $order)
                                @php
                                    $route = route('supplier.orders.update_shipping', $order->id);
                                    $cancel_route = route('supplier.orders.cancel', $order->id);
                                    $receipt_route = route('supplier.orders.upload_receipt', $order->id);

                                    //carrega a url e o código
                                    $shipping = \App\Models\SupplierOrderShippings::where('supplier_id', $order->supplier_id)
                                                        ->where('supplier_order_id', $order->id)
                                                        ->first();
                                    $delete_action = route('supplier.orders.destroy', $order->id);
                                    $frete_manual_action = route('supplier.orders.update_manual_melhor_envio', $order->id);
                                @endphp
                                <tr>
                                   
                                    <td id="order-id">#{{ $order->id }}<span style='display:none;'>#{{$order->id}}</span></td>
                                    <td>Impotado:{{ $order->order->external_service }} <br>
                                        Id. Ext:{{ $order->order->external_id }} <br>
                                        Cliente: {{ $order->order->customer->first_name.' '.$order->order->customer->last_name }}
                                    </td>
                                    
                                    
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
                                    <td>R$ {{ \App\Http\Controllers\Supplier\FunctionsController::supplierOrderAmount($order) }} <br>
                                    Data:{{ date('d/m/Y', strtotime($order->created_at)) }}
                                    </td>
                                    <td>{{ $order->shipping && $order->shipping->tracking_number ? $order->shipping->tracking_number : '' }} <br>
                                    @if($authenticated_user->shipping_method == 'melhor_envio')
                                        
                                            @if($order->frete_melhor_envio && $order->frete_melhor_envio->status)
                                                {{$arrMsgMelhorEnvio[$order->frete_melhor_envio->status]}}
                                            @else
                                                Não consta 
                                            @endif
                                       
                                    @endif


                                   </td>
                                   
                                    
                                    <td>
                                        <a href="{{ route('supplier.orders.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="Detalhes">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                        <a href="#!" class="btn btn-info btn-sm" data-toggle="modal" data-target="#upload-receipt-modal" onclick="uploadReceipt('{{ $receipt_route }}', {{ $order->id }})" tooltip="true" title="Upload de Nota Fiscal">
                                            <i class="fas fa-fw fa-receipt"></i>
                                        </a>
                                        {{-- <a href="{{ route('supplier.orders.print_content_declaration', $order->id) }}" target="_blank" class="btn btn-danger btn-sm" tooltip="true" title="Imprimir Declaração de Conteúdo">
                                            <i class="fas fa-fw fa-file"></i>
                                        </a> --}}
                                        
                                        @if ($authenticated_user->shipping_method == 'melhor_envio' && $order->frete_melhor_envio)
                                            <a href="{{$order->frete_melhor_envio->tag_url}}" target="_blank" class="btn btn-danger btn-sm" tooltip="true" title="Imprimir Etiqueta">
                                                <i class="fas fa-fw fa-tag"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('supplier.orders.print_tag', $order->id) }}" target="_blank" class="btn btn-danger btn-sm" tooltip="true" title="Imprimir Etiqueta">
                                                <i class="fas fa-fw fa-tag"></i>
                                            </a>
                                        @endif
                                        
                                        <div class="d-inline" style="position:relative;">
                                            <a href="{{ route('supplier.orders.update_shipping_e', $order->id) }}" class="btn btn-success btn-sm" id="mark-as-sent-button"  title="Marcar como enviado">
                                                <i class="fas fa-fw fa-shipping-fast"></i>
                                            </a>
                                        </div>
                                        @if($order->order->shipping_label)
                                        <a class='btn btn-primary btn-sm' download='{{$order->f_display_id}}-etiqueta' style='color: #fff' href='{{Storage::disk('public')->url($order->order->shipping_label->url_labels)}}' title='Baixar etiqueta' tooltip="true"><i class="fas fa-file-download"></i></a>
                                        @endif
                                        @if(Auth::guard('admin')->check())
                                            <a href="#!" data-toggle="modal" data-target="#frete-manual-melhor-supplier-order-modal" onclick="update_frete_manual_melhor_form_action('{{$frete_manual_action}}', '{{$order->f_display_id}}')" class="btn btn-danger btn-sm" role='button' style='background-color: #3c5163 !important; border-color: #3c5163 !important;'><i class="fas fa-wrench"></i></a>                                            
                                        @endif
                                        <a href="#!" data-toggle="modal" data-target="#delete-supplier-order-modal" onclick="update_delete_form_action('{{$delete_action}}', '{{$order->f_display_id}}')" class="btn btn-danger btn-sm" role='button'><i class="fas fa-times"></i></a>
                                        {{-- <a href="#!" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cancel-order-modal" onclick="cancelOrder('{{ $cancel_route }}')" tooltip="true" title="Cancelar pedido">
                                            <i class="fas fa-fw fa-times"></i>
                                        </a> --}}
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
    		</div>
    	</div>
    </div>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="update-shipping-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="updateShippingForm">
                @csrf
                <input type="hidden" name="status" value="sent">
                <div class="modal-header mt-0">
                    <h5 class="modal-title">Cadastrar dados do frete</h5>
                </div>
                <div class="modal-body mb-0">
                    @if($authenticated_user->total_express_settings)
                        <div id="pending-total-express-order">
                            <h4>Remessa da Total Express</h4>
                            <p>Para usuários da Total Express, você pode confirmar o envio da remessa para a Total Express e o código de rastreio será atualizado automaticamente assim que recebido.</p>

                            <div class="form-group">
                                <label class="control-label">Nota Fiscal Eletrônica (NFE)</label>
                                <input type="file" class="form-control" name="nfe" id='nfe_input' order_id="0">
                            </div>

                            <div class="d-flex flex-wrap w-100">
                                <button class="btn btn-success flex-grow-1" type="button" id="declaration-send-button" onclick="sendDeclarationToTotalExpress()">Confirmar remessa com Declaração</button>
                                <button class="btn btn-primary flex-grow-1" type="button" id="nfe-send-button" onclick="sendNFEToTotalExpress()">Confirmar remessa com NFe</button>
                            </div>
                        </div>
                        <div id="complete-total-express-order" style="display:none">
                            <div class="alert alert-warning">
                                Essa remessa já foi enviada à Total Express. O código de rastreio será atualizado automaticamente assim que providenciado pela transportadora.
                            </div>
                        </div>

                        <hr>

                        <h4>Informar código de rastreio manualmente</h4>
                    @endif
                    <p>Marcar pedido como enviado e enviar o código de rastreio do pedido para o lojista e ao cliente. O lojista e o cliente serão notificados quando o código de rastreio é atualizado.</p>
                    {{-- Update this order tracking number. The customer will be notified with the new tracking number. --}}
                    <div class="form-group">
                        <label class="control-label">Nome da Transportadora</label>
                        <input type="text" class="form-control" name="company" id='company' placeholder="Nome da transportadora responsável pela entrega." required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">URL de Rastreio</label>
                        <input type="text" class="form-control" name="tracking_url" id='tracking_url' placeholder="Link para onde o cliente usará o código de rastreio ou o link direto para a página de rastreio." required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Código de rastreio</label>
                        <input type="text" class="form-control" name="tracking_number" id='tracking_number' placeholder="Digite aqui o código de rastreio do pedido." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success">Marcar como enviado</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(Auth::guard('admin')->check())
    <div class="modal fade" role="dialog" tabindex="-1" id="frete-manual-melhor-supplier-order-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="GET" action="" id="updateFreightManualForm">
                    @csrf
                    <div class="modal-header mt-0">
                        <h5 class="modal-title">Cadastrar frete manualmente melhor envio</h5>
                    </div>
                    <div class="modal-body mb-0">
                        <div class="form-group">
                            <label class="control-label">Protocolo</label>
                            <input type="text" class="form-control" name="protocol" id='protocol' placeholder="Protocolo do frete gerado na melhor envio." required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>   

@endif

<div class="modal fade" tabindex="-1" role="dialog" id="delete-supplier-order-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="delete_form">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Excluir Pedido</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o pedido <span id='order-id-delete-display'></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('supplier.cancel') }}</button>
                    <button class="btn btn-danger">{{ __('supplier.delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- <div class="modal fade" role="dialog" tabindex="-1" id="cancel-order-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="cancelOrderForm">
                @csrf
                <div class="modal-header mt-0">
                    <h5 class="modal-title">Cancelar pedido</h5>
                </div>
                <div class="modal-body mb-0">
                    <p>Você tem certeza que deseja cancelar o pedido? O pedido será excluido de seu painel e o cliente e lojista serão notificados do cancelamento do pedido.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger">Cancelar pedido</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}
<div class="modal fade" tabindex="-1" role="dialog" id="mark_selected_sent">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{route('supplier.orders.update_shipping.selected')}}">
                @csrf
                <input type="hidden" name='arrSelectedOrdersSent' id='arrSelectedOrdersSent'>
                <div class="modal-header">
                    <h5 class="modal-title">Marcar Pedidos Como Enviados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Você tem certeza que deseja marcar os pedidos selecionados como enviados?</p>
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
    function update_delete_form_action(action, order_id){
        $('#order-id-delete-display').html(order_id)
        $("#delete_form").attr('action', action);
    }

    function update_frete_manual_melhor_form_action(action, order_id){
        $("#updateFreightManualForm").attr('action', action);
    }

    function sendDeclarationToTotalExpress(){
        let order_id = $("#nfe_input").attr('order_id');

        $.ajax({
            url: '/supplier/orders/total_express/send/' + order_id,
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if(data.status == 'success'){
                    $("#mark-as-sent-button[order_id='"+order_id+"']").attr('exported_total_express', 1);
                    $("#sent-total-express-icon[order_id='"+order_id+"']").show();
                    $("#declaration-send-button").text('Confirmar remessa');
                    $("#update-shipping-modal").modal('hide');

                    Swal.fire('Sucesso!', 'Remessa confirmada na Total Express. Você já pode enviar seu pacote com a etiqueta para a transportadora.', 'success');
                }else{
                    $("#update-shipping-modal").modal('hide');
                    Swal.fire('Erro!', 'Aconteceu algum problema ao confirmar o envio de sua remessa com esta nota fiscal, caso o erro persista, entre em contato com nosso suporte.', 'error');

                    $("#declaration-send-button").text('Confirmar remessa');
                }
            },
            error: function(data){
                Swal.fire('Erro!', 'Aconteceu algum problema ao confirmar o envio de sua remessa com esta nota fiscal, caso o erro persista, entre em contato com nosso suporte.', 'error');

                $("#declaration-send-button").text('Confirmar remessa');
            },
            xhr: function() { // Custom XMLHttpRequest
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) { // Avalia se tem suporte a propriedade upload
                    myXhr.upload.addEventListener('progress', function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);

                            $("#declaration-send-button").text(percentComplete+'%');

                            if (percentComplete === 100) {
                                $("#declaration-send-button").html('<i class="fas fa-spin fa-spinner"></i>');
                            }
                        }
                    }, false);
                }
                return myXhr;
            }
        });
    };

    function sendNFEToTotalExpress(){
        if(document.getElementById("nfe_input").files.length == 0){
            Swal.fire('Erro!', 'A nota fiscal eletrônica é obrigatória.', 'success');

            return false;
        }

        let input = document.getElementById('nfe_input');
        let formData = new FormData();
        let order_id = $("#nfe_input").attr('order_id');

        formData.append('order_receipt', input.files[0]);

        $.ajax({
            url: '/supplier/orders/total_express/send/' + order_id,
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if(data.status == 'success'){
                    $("#mark-as-sent-button[order_id='"+order_id+"']").attr('exported_total_express', 1);
                    $("#sent-total-express-icon[order_id='"+order_id+"']").show();
                    $("#nfe-send-button").text('Confirmar remessa');
                    $("#update-shipping-modal").modal('hide');

                    Swal.fire('Sucesso!', 'Remessa confirmada na Total Express. Você já pode enviar seu pacote com a etiqueta para a transportadora.', 'success');
                }else{
                    $("#update-shipping-modal").modal('hide');
                    Swal.fire('Erro!', 'Aconteceu algum problema ao confirmar o envio de sua remessa com esta nota fiscal, caso o erro persista, entre em contato com nosso suporte.', 'error');

                    $("#nfe-send-button").text('Confirmar remessa');
                }
            },
            error: function(data){
                Swal.fire('Erro!', 'Aconteceu algum problema ao confirmar o envio de sua remessa com esta nota fiscal, caso o erro persista, entre em contato com nosso suporte.', 'error');

                $("#nfe-send-button").text('Confirmar remessa');
            },
            xhr: function() { // Custom XMLHttpRequest
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) { // Avalia se tem suporte a propriedade upload
                    myXhr.upload.addEventListener('progress', function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);

                            $("#nfe-send-button").text(percentComplete+'%');

                            if (percentComplete === 100) {
                                $("#nfe-send-button").html('<i class="fas fa-spin fa-spinner"></i>');
                            }
                        }
                    }, false);
                }
                return myXhr;
            }
        });
    };

    function updateShipping(route, company, url, code, obj){
        $("#updateShippingForm").attr('action', route);

        $('#company').val(company);

        $('#tracking_url').val(url);
        //$("#tracking_url").prop( "disabled", true );

        $('#tracking_number').val(code);
        //$("#tracking_number").prop( "disabled", true );

        let order_id = $(obj).attr('order_id');
        $("#nfe_input").attr('order_id', order_id);

        let exported_to_te = $(obj).attr('exported_total_express');

        if(exported_to_te == 1){
            $("#pending-total-express-order").hide();
            $("#complete-total-express-order").show();
        }else{
            $("#pending-total-express-order").show();
            $("#complete-total-express-order").hide();
        }
    }

    function cancelOrder(route){
        $("#cancelOrderForm").attr('action', route);
    }

    var arrOrders = []

    $(document).ready( function () {
        setTimeout(function (){
            $('.dt-buttons').append(
                '<form id="formRastreioBling" action="{{route('supplier.orders.update_tracking_number_bling')}}" method="post">{{ csrf_field() }}'+
                    "<input type='hidden' name='arrOrdersId' id='arrOrdersId'>"+
                    "<a id='get-bling-orders' class='btn btn-sm btn-info' style='color: #fff;'><i class='fas fa-redo-alt'></i> Buscar Rastreio Bling Selecionados</a>"+
                "</form>")
            @if($authenticated_user->id == 56)
                $('.dt-buttons').append(
                    '<form id="formRastreioChinaDivision" action="{{route('supplier.orders.update_tracking_number_china_division')}}" method="post">{{ csrf_field() }}'+
                        "<input type='hidden' name='arrOrdersId' id='arrOrdersIdChinaDivision'>"+
                        "<a id='get-china-division-orders' class='btn btn-sm btn-primary ml-2' style='color: #fff;'><i class='fas fa-redo-alt'></i> Buscar Rastreio China Division Selecionados</a>"+
                    "</form>")
            @endif

            $('.dt-buttons').append("<a id='get-selected-sent-orders' data-toggle=\"modal\" data-target=\"#mark_selected_sent\" class='btn btn-sm btn-success ml-2' style='color: #fff;'><i class='fas fa-fw fa-shipping-fast'></i> Marcar Selecionados Como Enviados</a>")


            $('.paginate_button').attr('style', 'color: #8898aa !important');

            $('.current').attr('style', 'color: #fff !important');

            $('.buttons-select-all').addClass('btn btn-success btn-sm ml-4')

            $('.buttons-select-none').addClass('btn btn-secondary btn-sm')

        }, 500)

        $(document).on('mouseenter', '.paginate_button', function() {
            $(this).css("background-color", "#dee2e6").css("background", "transparent");
        })

        $(document).on('mouseleave', '.paginate_button',function() {
            $(this).css("background-color", "#fff").css("background", "transparent");
        });


        $(document).on('click', '.paginate_button', function(){
            $('.paginate_button').attr('style', 'color: #8898aa !important');
            $('.current').attr('style', 'color: #fff !important');
        })


        $(document).on('click','#get-bling-orders', function(){
            table.rows({ selected: true }).every( function () {
                var d = this.data();
                arrOrders.push(d[1].split("#")[1].split('<')[0])
                d.counter++; // update data source for the row
            });

            $('#arrOrdersId').val(arrOrders);
            if(arrOrders.length > 0){
                setTimeout(function() {
                    $("#formRastreioBling").submit();
                }, 1000);
            }
        })

        @if($authenticated_user->id == 56)
            $(document).on('click','#get-china-division-orders', function(){
                table.rows({ selected: true }).every( function () {
                    var d = this.data();
                    arrOrders.push(d[1].split("#")[1].split('<')[0])
                    d.counter++; // update data source for the row
                });

                $('#arrOrdersIdChinaDivision').val(arrOrders);

                setTimeout(function() {
                    $("#formRastreioChinaDivision").submit();
                }, 1000);
            })
        @endif
        
        //marcar vários pedidos como enviados
        $(document).on('click','#get-selected-sent-orders', function(){
            arrOrders = []

            table.rows({ selected: true }).every( function () {
                var d = this.data();
                arrOrders.push(d[1].split("#")[1].split('<')[0])
                d.counter++; // update data source for the row
            });

            $('#arrSelectedOrdersSent').val(arrOrders);
        })

        //marcar pega os selecionados para imprimir a etiqueta e declaração de conteúdo
        $(document).on('click','#btn-print-tags-melhor-envio', function(){

        // Inicializa a matriz "arrOrders"
            arrOrders = []

            // Seleciona todas as linhas da tabela que estão atualmente marcadas como selecionadas e executa uma função para cada uma delas
            table.rows({ selected: true }).every( function () {

            // Obtém os dados da linha atual
            var d = this.data();

            // Obtém o número do pedido da segunda coluna da linha e adiciona à matriz "arrOrders"
            arrOrders.push(d[0].split("#")[1].split('<')[0])

            // Atualiza o contador de linha (aparentemente não está sendo usado)
            d.counter++; // update data source for the row
            });

            // Define o valor do elemento com o ID "#print_tags_melhor_envio" como a matriz "arrOrders"
            $('#print_tags_melhor_envio').val(arrOrders);

            // Define um atraso de 1 segundo antes de enviar um formulário com o ID "#form-print-tags-melhor-envio"
            setTimeout(function() {
            $("#form-print-tags-melhor-envio").submit();
            }, 1000);

            });

        var table = $('table').DataTable({
            dom: 'Bfrtip',
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0,
                // render: function (data, type, full, meta){
                //     return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
                // }
            } ],
            select: {
                style:    'multi',
                selector: 'td:first-child'
            },
            buttons: [
                'selectAll',
                'selectNone'
            ],
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
                },
                buttons: {
                    selectAll: "<i class=\"far fa-check-square\"></i> Selecionar todos",
                    selectNone: "<i class=\"fas fa-minus-circle\"></i> Remover Seleção"
                }
            },
            "order": [[ 0, 'desc' ]],
        });
    } );
</script>
@endsection
