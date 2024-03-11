@extends('shop.layout.default')

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-default"></span>
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Pendentes</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ $countOrders }}</span>
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Valor Total</h5>
                                    <span class="h2 font-weight-bold mb-0">R$ {{ number_format($orders->sum('amount'), 2, ',','.') }}</span>
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
                            <h2 class="mb-0">Carrinho</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <p>
                       
                        Listagem de produtos com pagamento pendente. Você pode marcar os produtos que deseja pagar e efetuar um pagamento em lote. Assim que o pagamento for confirmado irá iniciar o processo de envio.<br>
                        </p>

                        <div class="w-100 d-flex flex-wrap align-items-center justify-content-end mt-3">

                         
                            @if(isset($apimercadolivreapi))
                            <br>                             
                            <a href="{{ route('shop.orders.mercadolivre') }}" class="btn btn-primary" style="background-color: #77d77;">Importar Mercadolivre</a>
                            @endif                               
                            
                            
                        </div>
                    </div>
                </div>
                <form method="GET" action="{{route('shop.orders.pending.search')}}">
                    <div class="ml-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">                                
                                    <input class="form-control" type="search" name='query' placeholder='Digite algo para buscar...' id="search-query" value='{{isset($query) ? $query : ''}}'>
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
                                <input type="radio" class="custom-control-input" id='filtro-cliente' value='customer' name='filter' {{isset($filter) && $filter == 'customer' ? 'checked' : ''}} {{!isset($filter) ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-cliente">Cliente</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id='filtro-id' value='name' name='filter' {{isset($filter) && $filter == 'name' ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-id">ID</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id='filtro-data-importacao' value='created_at' name='filter' {{isset($filter) && $filter == 'created_at' ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-data-importacao">Data Importação</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id='filtro-data-origem' value='external_created_at' name='filter' {{isset($filter) && $filter == 'external_created_at' ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-data-origem">Data na Origem</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id='filtro-ref-externa' value='external_id' name='filter' {{isset($filter) && $filter == 'external_id' ? 'checked' : ''}}>
                                <label class="custom-control-label" for="filtro-ref-externa">Ref. Externa</label>
                            </div>
                    </div>
                </form>
				<div class="table-responsive" id='table-search'>
                    @if(isset($countOrdersSearch))
                        <div class='ml-4'>
                            <span class='small'>Resultados: <b>{{$orders->total()}}</b></span>
                        </div>
                    @endif
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th style="width:30px" class="no-sort"><input type="checkbox" class="select_all"></th>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Ref. Externa/Valor</th>
                                <th>Cliente</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                @php
                                    $items_amount = 0.0;
                                    $items_external_amount = 0.0;
                                    $flagDolar = false;
                                    //$dolar_price = 0.0;
                                    foreach($order->items as $item){
                                        if($item->variant && $item->variant->product){ //caso a variante ainda exista, faz normal
                                            //caso o produto seja em dólar, realiza a cotação e soma com o valor do dólar atual
                                            if($item->variant->product->currency == 'US$'){
                                                //$dolar_price = App\Services\CurrencyService::getDollarPrice();
                                                if(isset($dolar_price['price'])){
                                                    $items_amount += $item->amount * $dolar_price['price'];
                                                    $items_external_amount += $item->external_price * $dolar_price['price'];
                                                    $flagDolar = true; //caso tenha algum item em dólar exibe a mensagem para a pessoa saber da variação
                                                }else{
                                                    $items_amount += $item->amount * 1000;
                                                    $items_external_amount += $item->external_price * 1000;
                                                }
                                            }else{
                                                $items_amount += $item->amount;
                                                $items_external_amount += $item->external_price;
                                            }
                                        }else{ //caso contrário, busca ela mesmo que excluída
                                            $variant = \App\Models\ProductVariants::withTrashed()->find($item->product_variant_id);

                                            if($variant){
                                                $product = \App\Models\Products::withTrashed()->find($variant->product_id);
                                                
                                                if($product && $product->currency == 'US$'){
                                                    //$dolar_price = App\Services\CurrencyService::getDollarPrice();
                                                    if(isset($dolar_price['price'])){
                                                        $items_amount += $item->amount * $dolar_price['price'];
                                                        $items_external_amount += $item->external_price * $dolar_price['price'];
                                                        $flagDolar = true; //caso tenha algum item em dólar exibe a mensagem para a pessoa saber da variação
                                                    }else{
                                                        $items_amount += $item->amount * 1000;
                                                        $items_external_amount += $item->external_price * 1000;
                                                    }
                                                }else{
                                                    $items_amount += $item->amount;
                                                    $items_external_amount += $item->external_price;
                                                }
                                            }
                                        }
                                    }
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        @if($order->supplier_order_created == 0)
                                            @if($order->customer)
                                                <input type="checkbox" class="order_checkbox" order_id="{{ $order->id }}" name="orders[{{ $order->id }}]">
                                            @else
                                                <i class="fas fa-exclamation-circle text-danger" tooltip="true" title="Não é possível efetuar o pagamento deste pedido pois não há nenhum cliente atribuido à ele. Entre em contato com nossa equipe para resolver o problema."></i>
                                            @endif
                                        @else
                                            #
                                        @endif
                                    </td>
                                    <td>{{ $order->id }}</td>
                                    <td>Importação: {{ date('d/m/Y', strtotime($order->created_at)) }} <br>
                                        Origem:
                                        @if ($order->external_created_at)
                                            {{ date('d/m/Y', strtotime($order->external_created_at)) }}
                                        @endif
                                    </td>
                                    
                                    @if($order->external_service == 'shopify')
                                    <td>- {{ ucfirst($order->external_service) }}: <a href="https://{{ $authenticated_user->shopify_app->domain }}.myshopify.com/admin/orders/{{ $order->external_id }}" target="_blank">#{{ $order->external_id }}</a>
                                    <br>
                                    - Fornecedor: R$ {{ number_format($items_amount, 2, ',', '.') }} {{-- number_format($order->amount, 2, ',', '.') --}} <br>
                                    - {{ucfirst($order->external_service)}}: R$ {{-- number_format($items_external_amount, 2, ',', '.') --}} {{ number_format($order->external_price, 2, ',', '.') }}
                                   </td>
                                    @endif
                                    @if($order->external_service == 'cartx')
                                    <td>- {{ ucfirst($order->external_service) }}: <a href="https://accounts.cartx.io/orders/details/{{ $order->external_id }}" target="_blank">#{{ $order->external_id }}</a>
                                    <br>
                                    - Fornecedor: R$ {{ number_format($items_amount, 2, ',', '.') }} {{-- number_format($order->amount, 2, ',', '.') --}} <br>
                                    - {{ucfirst($order->external_service)}}: R$ {{-- number_format($items_external_amount, 2, ',', '.') --}} {{ number_format($order->external_price, 2, ',', '.') }}
                                    </td>
                                    @endif
                                    @if($order->external_service == 'woocommerce')
                                    <td>- {{ ucfirst($order->external_service) }}: <a href="{{ $authenticated_user->woocommerce_app->domain }}/orders/details/{{ $order->external_id }}" target="_blank">#{{ $order->external_id }}</a>
                                    <br>  
                                    - Fornecedor: R$ {{ number_format($items_amount, 2, ',', '.') }} {{-- number_format($order->amount, 2, ',', '.') --}} <br>
                                    - {{ucfirst($order->external_service)}}: R$ {{-- number_format($items_external_amount, 2, ',', '.') --}} {{ number_format($order->external_price, 2, ',', '.') }}
                                   </td>
                                    @endif
                                    @if($order->external_service == 'planilha')
                                    <td>- Planilha <br>
                                    - Fornecedor: R$ {{ number_format($items_amount, 2, ',', '.') }} {{-- number_format($order->amount, 2, ',', '.') --}} <br>
                                    - {{ucfirst($order->external_service)}}: R$ {{-- number_format($items_external_amount, 2, ',', '.') --}} {{ number_format($order->external_price, 2, ',', '.') }}
                                    </td>
                                    @endif
                                    @if($order->external_service == 'yampi')
                                    <td>- Yampi  
                                    - Fornecedor: R$ {{ number_format($items_amount, 2, ',', '.') }} {{-- number_format($order->amount, 2, ',', '.') --}} <br>
                                    - {{ucfirst($order->external_service)}}: R$ {{-- number_format($items_external_amount, 2, ',', '.') --}} {{ number_format($order->external_price, 2, ',', '.') }}
                              
                                    </td>
                                    @endif                                    
                                    @if($order->external_service == 'bling_service')
                                    <td>- {{ ucfirst($order->external_service) }}: <a href="#" target="_blank">#{{ $order->external_id }}</a>
                                    <br>
                                    - Fornecedor: R$ {{ number_format($items_amount, 2, ',', '.') }} {{-- number_format($order->amount, 2, ',', '.') --}} <br>
                                    - {{ucfirst($order->external_service)}}: R$ {{-- number_format($items_external_amount, 2, ',', '.') --}} {{ number_format($order->external_price, 2, ',', '.') }}
                              
                                
                                    </td>
                                    
                                    
                                    @endif 
                                    @if($order->external_service == 'mercadolivre')
                                    <td>- Mercadolivre <br>
                                    - Fornecedor: R$ {{ number_format($items_amount, 2, ',', '.') }} {{-- number_format($order->amount, 2, ',', '.') --}} <br>
                                    - {{ucfirst($order->external_service)}}: R$ {{-- number_format($items_external_amount, 2, ',', '.') --}} {{ number_format($order->external_price, 2, ',', '.') }}
                              
                                   </td>
                                    @endif
                                    <td class="text-gray">
                                        - {{ $order->customer->first_name.' '.$order->customer->last_name }}<br>
                                        - {{ $order->customer->email }}<br>
                                        - {{ $order->customer->address->phone }}<br>
                                        - {{ $order->customer->address->address1}}<br> 
                                        - {{ $order->customer->address->address2.'-'.$order->customer->address->city.'/'.$order->customer->address->privince_code.'-'.$order->customer->address->zipcode }}
                                    </td>
                                    <td>
                                        <a href="{{ route('shop.orders.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="Detalhes">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                        
                                          @if($order->external_service == 'bling_service')
                                        <a href="{{ route('shop.orders.update_tracking_number_bling' , $order->id) }}" class="btn btn-success btn-sm" tooltip="true" title="Importar Rastreamento Bling">
                                            <i class="fas fa-fw fa-truck"></i>
                                        </a>
                                        @endif
                                        
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Carrinho vazio.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {!! $orders->appends(request()->query())->render() !!}
                    <div class="card-body">
                        <div class="float-right mb-4">
                            <button class="btn btn-success" id="pay_selected_orders">Finalizar compra</button>
                        </div>
                    </div>
                </div>
    		</div>
    	</div>
    </div>
</div>

{{-- Modal Import CSV --}}
<div class="modal" tabindex="-1" role="dialog" id="CsvModal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Importar Planilha</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
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
                "paging":   false,
                "ordering": true,
                "info":     false,
                "searching": false
            });
        } );

        $('.select_all').change(function(){
            if($(this).is(':checked')){
                $('.order_checkbox').prop('checked', true);
            }else{
                $('.order_checkbox').prop('checked', false);
            }
        });


        $('input[name="file"]').change(function(e){
            var fileName = e.target.files[0].name;
            $('.order-file').html(fileName);
        });

        $('input[name="file_shipping_labels"]').change(function(e){
            var fileName = e.target.files[0].name;
            $('.order-file-shipping-label').html(fileName);
        });
        
        $('input[name="fileItem"]').change(function(e){
            var fileName = e.target.files[0].name;
            $('.order-item').html(fileName);
        });


        $('#pay_selected_orders').click(function(){

           var order_ids = [];
            $('.order_checkbox:checked').each(function(index, element){
                order_ids.push($(element).attr('order_id'))
            })

            if(order_ids.length > 0){
                $.ajax({
                    url: '{{ route('shop.orders.prepare_payment') }}',
                    method: 'POST',
                    data: {order_ids : order_ids},
                    beforeSend: function(){
                        $('#pay_selected_orders').html('Carregando...')
                        $('#pay_selected_orders').attr('disabled', true)
                    },
                    success: function(data){
                        // console.log('Pedidos a pagar:')
                        // console.log(data)
                        //location.reload()
						 location.href= "/shop/orders/invoices/pending";
                    },
                    error: function(data){
                        $('#pay_selected_orders').html('Finalizar compra')
                        $('#pay_selected_orders').attr('disabled', false)
                        //console.log(data)
                    }
                })
            }else{
                Swal.fire("Atenção", "Selecione ao menos um item para pagar.", 'warning');
            }
        });
    </script>
@endsection
