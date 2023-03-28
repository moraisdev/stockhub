@extends('shop.layout.default')

@section('title', config('app.name').' - Detalhes da compra')

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
                            <h2 class="mb-0">Detalhes da compra</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <p>
                                <b>Origem do pedido:</b> {{ ucfirst($order->external_service) }} <br>
                                @if($order->external_service == 'shopify')
                                <b>ID externo:</b> <a href="https://{{ $authenticated_user->shopify_app->domain }}.myshopify.com/admin/orders/{{ $order->external_id }}" target="_blank">#{{ ucfirst($order->external_id) }}</a> <br>
                                @endif
                                <b>Nome do pedido:</b> {{ $order->name }} <br>
                            </p>
                        </div>
                        <div class="col">
                            <p>
                                @php
                                    $items_amount = 0.0;
                                    $items_external_amount = 0.0;
                                    $flagDolar = false;
                                    //$dolar_price = 0.0;
                                    foreach($order->items as $item){
                                        if($item->variant && $item->variant->product){ //caso o produto não tenha sido excluído
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
                                        }else{
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
                                
                                <b>Valor vendido na loja:</b> R$ {{number_format($order->external_price,2,',','.')}} {{--number_format($items_external_amount,2,',','.')--}}<br>
                                <b>Valor em produtos:</b> R$ {{number_format($items_amount,2,',','.')}} {!! $flagDolar ? "<span style='font-size: 9pt;'>(* Os valores podem variar de acordo com a cotação atual do dólar)</span>" : '' !!} {{-- number_format($order->items_amount,2,',','.') --}} <br>
                                @if($order->shipping_amount)
                                    <b>Valor do frete: </b> R$ {{ number_format($order->shipping_amount,2,',','.') }} <br>
                                @endif
                                <b>Total do pedido: </b> R$ {{ number_format($items_amount + $order->shipping_amount,2,',','.') }} <br>
                                <b>Status do pagamento aos fornecedores:</b> {{ $order->f_status }} <br>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow mt-4">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center w-100">
                                <h2 class="flex-grow-1 mb-0">Dados do cliente</h2>
                                @if($order->supplier_order_created == 0)
                                <button class="btn btn-sm btn-primary update-customer-button" data-toggle="modal" data-target="#update-customer-modal">
                                    <i class="fas fa-pencil-alt"></i> Editar endereço
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($order->customer)
                            <div class="col">
                                <p>
                                    @if($order->external_service == 'shopify')
                                    <b>ID externo do cliente:</b> <a href="https://{{ $authenticated_user->shopify_app->domain }}.myshopify.com/admin/customers/{{ $order->customer->external_id }}" target="_blank">#{{ $order->customer->external_id }}</a> <br>
                                    @endif
                                    <b>Nome:</b> {{ $order->customer->address->name }} <br>
                                    <b>E-mail:</b> {{ $order->customer->email }} <br>
                                    <b>Telefone:</b> {{ $order->customer->address->phone }} <br>
                                    <b>CPF:</b> {{ $order->customer->cpf }} <br>
                                </p>
                            </div>
                            <div class="col">
                                <p>
                                    <b>Endereço:</b> <span id="customer-address1">{{ $order->customer->address->address1 }}</span> <br>
                                    @if($order->customer->address->address2)
                                        <span id="customer-address2">{{ $order->customer->address->address2 }}</span><br>
                                    @endif
                                    @if($order->customer->address->company)
                                        <span id="customer-company">{{ $order->customer->address->company }}</span><br>
                                    @endif
                                    <b>CEP:</b> <span id="customer-zipcode">{{ $order->customer->address->zipcode }}</span> <br>
                                    <b>Cidade:</b> <span id="customer-city">{{ $order->customer->address->city }}</span> <br>
                                    <b>Estado:</b> <span id="customer-province">{{ $order->customer->address->province }}</span> <br>
                                </p>
                            </div>
                        @else
                            <div class="col">
                                <div class="alert alert-danger">
                                    Não há nenhum cliente ligado à esse pedido e não é possível concluí-lo no momento. Entre em contato com a nossa equipe para resolver este pedido.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card shadow mt-4">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center w-100">
                                <h2 class="flex-grow-1 mb-0">Produtos</h2>
                                @if($order->supplier_order_created == 0)
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#add-item-modal">
                                    <i class="fas fa-plus"></i> Add produto
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th class="text-center">Ações</th>
                                <th>SKU</th>
                                <th>Custo Unitário do Produto</th>
                                <th>Produto</th>
                                <th>NCM</th>
                                <th>Desconto Aplicado</th>
                                <th>Qtd</th>
                                <th>Valor a pagar</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->items as $item)
                                @if($item->variant && $item->variant->product)
                                    <tr>
                                        <td class="text-center">
                                            @if($order->supplier_order_created == 0)
                                                <button class="btn btn-primary btn-sm update-item-button" data-toggle="modal" data-target="#update-item-modal" item_id="{{ $item->id }}">
                                                    <i class="fas fa-fw fa-pencil-alt"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm remove-item-button" data-toggle="modal" data-target="#delete-item-modal" item_id="{{ $item->id }}">
                                                    <i class="fas fa-fw fa-times"></i>
                                                </button>
                                            @else
                                                #
                                            @endif
                                        </td>
                                        <td>{{ $item->sku }}</td>
                                        {{-- <td>R$ {{ number_format($item->external_price,2,',','.') }}</td> --}}
                                        <td>
                                            @if($item->variant)
                                                @if($item->variant->product->currency == 'US$')
                                                    R$ {{ number_format($item->variant->price * $dolar_price['price'],2,',','.') }}
                                                @else
                                                    R$ {{ number_format($item->variant->price,2,',','.') }}
                                                @endif                                    
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->external_service == 'shopify')
                                            <a href="https://{{ $authenticated_user->shopify_app->domain }}.myshopify.com/admin/products/{{ $item->external_product_id }}/variants/{{ $item->external_variant_id }}" target="_blank">{{ $item->variant ? $item->variant->title : '#' }}</a></td>
                                            @elseif($order->external_service == 'cartx')
                                            <a href="https://accounts.cartx.io/products/edit/{{ $item->external_product_id }}/variants/{{ $item->external_variant_id }}" target="_blank">{{ $item->variant ? $item->variant->title : '#' }}</a></td>
                                            @elseif($order->external_service == 'woocommerce')
                                            <a href="{{ $authenticated_user->woocommerce_app->domain }}/wp-admin/post.php?post={{ $item->external_product_id }}&action=edit" target="_blank">{{ $item->variant ? $item->variant->title : '#' }}</a></td>
                                            @else
                                            <a href="{{route('shop.products.show', ['product' => $item->variant->product->id])}}" target="_blank">{{ $item->variant ? $item->variant->title : '#' }}</a></td>
                                            @endif
                                            
                                        <td>{{ $item->ncm }}</td>
                                        <td>
                                        @if($item->discount_applied) {{-- Caso possua descontos, ve qual desconto foi aplicado nessa venda --}}
                                            {{($item->discount_applied->discount->value)}}%
                                        @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        @if($item->variant)
                                            @if($item->variant->product->currency == 'US$')
                                                <td>R$ {{ number_format($item->variant->price * $dolar_price['price'] * $item->quantity,2,',','.') }}</td>
                                            @else
                                                <td>R$ {{ number_format($item->variant->price * $item->quantity,2,',','.') }}</td>
                                            @endif                                    
                                        @else
                                            <td>R$ 0,00</td>
                                        @endif                                    
                                    </tr>
                                @else
                                    @php
                                        $variant = \App\Models\ProductVariants::withTrashed()->find($item->product_variant_id);
                                        if($variant){
                                            $product = \App\Models\Products::withTrashed()->find($variant->product_id);
                                        }
                                    @endphp
                                    
                                    @if($variant && $product)
                                        <tr>
                                            <td class="text-center">
                                                @if($order->supplier_order_created == 0)
                                                    <button class="btn btn-primary btn-sm update-item-button" data-toggle="modal" data-target="#update-item-modal" item_id="{{ $item->id }}">
                                                        <i class="fas fa-fw fa-pencil-alt"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm remove-item-button" data-toggle="modal" data-target="#delete-item-modal" item_id="{{ $item->id }}">
                                                        <i class="fas fa-fw fa-times"></i>
                                                    </button>
                                                @else
                                                    #
                                                @endif
                                            </td>
                                            <td>{{ $item->sku }}<br><small>O produto <b>{{$variant->title }}</b> não está mais disponível</small></td>
                                            {{-- <td>R$ {{ number_format($item->external_price,2,',','.') }}</td> --}}
                                            <td>
                                                @if($variant)
                                                    @if($product->currency == 'US$')
                                                        R$ {{ number_format($variant->price * $dolar_price['price'],2,',','.') }}
                                                    @else
                                                        R$ {{ number_format($variant->price,2,',','.') }}
                                                    @endif                                    
                                                @endif
                                            </td>
                                            <td>
                                                @if($order->external_service == 'shopify')
                                                <a href="https://{{ $authenticated_user->shopify_app->domain }}.myshopify.com/admin/products/{{ $item->external_product_id }}/variants/{{ $item->external_variant_id }}" target="_blank">{{ $variant ? $variant->title : '#' }}</a></td>
                                                @endif
                                                @if($order->external_service == 'cartx')
                                                <a href="https://accounts.cartx.io/products/edit/{{ $item->external_product_id }}/variants/{{ $item->external_variant_id }}" target="_blank">{{ $variant ? $variant->title : '#' }}</a></td>
                                                @endif
                                                
                                            <td>{{ $item->ncm }}</td>
                                            <td>
                                            @if($item->discount_applied) {{-- Caso possua descontos, ve qual desconto foi aplicado nessa venda --}}
                                                {{($item->discount_applied->discount->value)}}%
                                            @endif
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            @if($variant)
                                                @if($product->currency == 'US$')
                                                    <td>R$ {{ number_format($variant->price * $dolar_price['price'] * $item->quantity,2,',','.') }}</td>
                                                @else
                                                    <td>R$ {{ number_format($variant->price * $item->quantity,2,',','.') }}</td>
                                                @endif                                    
                                            @else
                                                <td>R$ 0,00</td>
                                            @endif                                    
                                        </tr>
                                    @endif
                                @endif
                                
                            @empty
                                <tr>
                                    <td colspan="7">Nenhum produto ligado à esse pedido.</td>
                                </tr>
                            @endforelse
                            <tr>
                                <th colspan="7" class="text-right">Subtotal</th>
                                <td>R$ {{ number_format($items_amount, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th colspan="7" class="text-right">Frete</th>
                                <td>R$ {{ number_format($order->shipping_amount, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th colspan="7" class="text-right">Total</th>
                                <td>R$ {{ number_format($items_amount + $order->shipping_amount, 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
    		</div>
            @if($order->shipping_label)
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">Etiqueta</h2>
                                <a class='btn btn-primary mt-3' download='{{$order->name}}-etiqueta' style='color: #fff' href='{{asset('etiqueta/'. $order->shipping_label->url_labels)}}'><i class="fas fa-file-download"></i> Baixar etiqueta</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="card shadow mt-4">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">Pedidos gerados a partir desta compra</h2>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fornecedor</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Produtos</th>
                                <th>Cód. Rastreio</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($order->supplier_orders as $sup_order)
                            <tr>
                                <td>
                                    <a href="{{ route('shop.orders.ask_return', $sup_order->id) }}" class="btn btn-warning btn-sm" tooltip="true" title="Solicitar estorno">
                                        <i class="fas fa-fw fa-undo"></i>
                                    </a>
                                </td>
                                <td>{{ $sup_order->supplier->name }}</td>
                                <td>{{ number_format($sup_order->total_amount, 2, ',', '') }}</td>
                                <td>{{ $sup_order->f_status }}</td>
                                <td>
                                    <div class="avatar-group">
                                        @foreach($sup_order->items as $item)
                                            @if($item->variant)
                                                <a href="#" class="avatar avatar-sm" tooltip="true" title="{{ $item->quantity.'x '.$item->variant->title }}">
                                                    <img alt="{{ $item->variant->title }}" src="{{ ($item->variant->img_source) ? $item->variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="rounded-circle bg-white w-100 h-100">
                                                </a>
                                            @else
                                                @php
                                                    $variant = \App\Models\ProductVariants::withTrashed()->find($item->product_variant_id);
                                                @endphp
                                                @if($variant)
                                                    <a href="#" class="avatar avatar-sm" tooltip="true" title="{{ $item->quantity.'x '.$variant->title }}">
                                                        <img alt="{{ $variant->title }}" src="{{ ($variant->img_source) ? $variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="rounded-circle bg-white w-100 h-100">
                                                    </a>
                                                    <br><small>O produto <b>{{$variant->title }}</b> não está mais disponível</small>
                                                @endif
                                            @endif
                                            
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    @if($sup_order->shipping && $sup_order->shipping->tracking_number)
                                        {{ $sup_order->shipping->tracking_number }}
                                    @else
                                        Não disponível
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Nenhum pedido enviado a fornecedores referente à essa compra.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="float-right mt-4">
                <a href="{{ route('shop.orders.cancel', $order->id) }}" class="btn btn-danger">Cancelar pedido</a>
            </div>
    	</div>
    </div>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="update-customer-modal">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('shop.orders.update_customer', $order->id) }}" id="update-customer-form">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title mb-0">Editar endereço do cliente</h3>
                </div>
                <div class="modal-body py-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Endereço</label>
                                <input type="text" class="form-control" name="address1" id="customer-address1-input" placeholder="Endereço">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Complemento</label>
                                <input type="text" class="form-control" name="address2" id="customer-address2-input" placeholder="Complemento">
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="form-group">
                                <label>CPF</label>
                                <input type="text" class="form-control" name="company" id="customer-company-input" placeholder="Documento do cliente">
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="form-group">
                                <label>CEP</label>
                                <input type="text" class="form-control" name="zipcode" id="customer-zipcode-input" placeholder="CEP">
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="form-group">
                                <label>Cidade</label>
                                <input type="text" class="form-control" name="city" id="customer-city-input" placeholder="Cidade">
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="form-group">
                                <label>Estado</label>
                                <input type="text" class="form-control" name="province" id="customer-province-input" placeholder="Estado">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Alterar endereço</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="add-item-modal">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('shop.orders.add_item', $order->id) }}" id="add-item-form">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title mb-0">Adicionar produto</h3>
                </div>
                <div class="modal-body py-0">
                    <p>Selecione um produto para adicioná-lo ao pedido.</p>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Produto</label>
                                <select class="form-control" name="variant_id" id="add-item-select" required>
                                    <option value="">Selecione um produto</option>
                                    @foreach($variants as $variant)
                                        <option value="{{ $variant->id }}">{{ $variant->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-6 col-12">
                            <div class="form-group">
                                <label>Preço externo</label>
                                <input type="text" class="form-control decimal" name="external_price" id="add-item-external-price" placeholder="Preço externo unitário (shopify)">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-12">
                            <div class="form-group">
                                <label>Quantidade</label>
                                <input type="number" class="form-control" name="quantity" id="add-item-quantity" placeholder="Quantidade">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Adicionar produto</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="update-item-modal">
    <div class="modal-dialog" role="document">
        <form method="POST" action="#" id="update-item-form">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title mb-0">Editar produto</h3>
                </div>
                <div class="modal-body py-0">
                    <div class="row">
                        <div class="col-xl-8 col-lg-6 col-12">
                            <div class="form-group">
                                <label>Preço externo</label>
                                <input type="text" class="form-control decimal" name="external_price" id="product-external-price" placeholder="Preço externo unitário ({{$order->external_service}})">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-12">
                            <div class="form-group">
                                <label>Quantidade</label>
                                <input type="number" class="form-control" name="quantity" id="product-quantity" placeholder="Quantidade">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Alterar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="delete-item-modal">
    <div class="modal-dialog" role="document">
        <form method="POST" action="#" id="delete-item-form">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title mb-0">Remover produto</h3>
                </div>
                <div class="modal-body py-0">
                    <p>Você tem certeza que deseja remover este produto do pedido?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger">Remover produto</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(".update-item-button").on('click', function(){
            let item_id = $(this).attr('item_id');

            $("#update-item-form").attr('action', '/shop/orders/items/'+item_id+'/update_item');
        });

        $(".remove-item-button").on('click', function(){
            let item_id = $(this).attr('item_id');

            $("#delete-item-form").attr('action', '/shop/orders/items/'+item_id+'/remove_item');
        });

        $(".update-customer-button").on('click', function(){
            let address1 = $("#customer-address1").text();
            let address2 = $("#customer-address2").text();
            let company = $("#customer-company").text();
            let zipcode = $("#customer-zipcode").text();
            let city = $("#customer-city").text();
            let province = $("#customer-province").text();

            $("#customer-address1-input").val(address1);
            $("#customer-address2-input").val(address2);
            $("#customer-company-input").val(company);
            $("#customer-zipcode-input").val(zipcode);
            $("#customer-city-input").val(city);
            $("#customer-province-input").val(province);
        });
    </script>
@endsection
