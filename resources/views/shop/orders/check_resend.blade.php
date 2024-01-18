@extends('shop.layout.default')

@section('title', config('app.name').' - Detalhes do reenvio')

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
                            <h2 class="mb-0">Checar dados do pedido</h2>
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
                                
                                <b>Valor em produtos:</b> R$ {{number_format($orderReturned->supplier_order->amount,2,',','.')}} {!! $flagDolar ? "<span style='font-size: 9pt;'>(* Os valores podem variar de acordo com a cotação atual do dólar)</span>" : '' !!} {{-- number_format($order->items_amount,2,',','.') --}} <br>
                                @if($orderReturned->supplier_order->shipping->amount)
                                    <b>Valor do frete: </b> R$ {{ number_format($orderReturned->supplier_order->shipping->amount,2,',','.') }} <br>
                                    {{-- {{dd($orderReturned->supplier_order->amount)}} --}}
                                    {{-- {{dd($orderReturned->supplier_order->shipping->amount)}} --}}
                                @endif
                                {{-- <b>Total do pedido: </b> R$ {{ number_format($orderReturned->supplier_order->amount + $orderReturned->supplier_order->shipping->amount,2,',','.') }} --}}
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
                    
                    <p>Aqui você pode corrigir os dados de endereço do cliente, caso necessário</p>
                    <form action="{{route('shop.orders.generate_resend_invoice')}}" method='post' enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_returned_id" value='{{$orderReturned->id}}'>
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Endereço</label>
                                    <input type="text" class="form-control" name="address1" id="customer-address1-input" placeholder="Endereço" value='{{ $order->customer->address->address1 }}'>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Número</label>
                                    <input type="text" class="form-control" name="address2" id="customer-address2-input" placeholder="Complemento" value='{{ $order->customer->address->address2 }}'>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>CPF</label>
                                    <input type="text" class="form-control" name="company" id="customer-company-input" placeholder="Documento do cliente" value='{{ $order->customer->address->company }}'>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>CEP</label>
                                    <input type="text" class="form-control" name="zipcode" id="customer-zipcode-input" placeholder="CEP" value='{{ $order->customer->address->zipcode }}'>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>Cidade</label>
                                    <input type="text" class="form-control" name="city" id="customer-city-input" placeholder="Cidade" value='{{ $order->customer->address->city }}'>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                @php
                                    $provice_codes = (object)[
                                        (object)['code' => "AC", 'name' => 'Acre'],
                                        (object)['code' => "AL", 'name' => 'Alagoas'],
                                        (object)['code' => "AP", 'name' => 'Amapá'],
                                        (object)['code' => "AM", 'name' => 'Amazonas'],
                                        (object)['code' => "BA", 'name' => 'Bahia'],
                                        (object)['code' => "CE", 'name' => 'Ceará'],
                                        (object)['code' => "DF", 'name' => 'Distrito Federal'],
                                        (object)['code' => "ES", 'name' => 'Espírito Santo'],
                                        (object)['code' => "GO", 'name' => 'Goiás'],
                                        (object)['code' => "MA", 'name' => 'Maranhão'],
                                        (object)['code' => "MT", 'name' => 'Mato Grosso'],
                                        (object)['code' => "MS", 'name' => 'Mato Grosso do Sul'],
                                        (object)['code' => "MG", 'name' => 'Minas Gerais'],
                                        (object)['code' => "PA", 'name' => 'Pará'],
                                        (object)['code' => "PB", 'name' => 'Paraíba'],
                                        (object)['code' => "PR", 'name' => 'Paraná'],
                                        (object)['code' => "PE", 'name' => 'Pernambuco'],
                                        (object)['code' => "PI", 'name' => 'Piauí'],
                                        (object)['code' => "RJ", 'name' => 'Rio de Janeiro'],
                                        (object)['code' => "RN", 'name' => 'Rio Grande do Norte'],
                                        (object)['code' => "RS", 'name' => 'Rio Grande do Sul'],
                                        (object)['code' => "RO", 'name' => 'Rondônia'],
                                        (object)['code' => "RR", 'name' => 'Roraima'],
                                        (object)['code' => "SC", 'name' => 'Santa Catarina'],
                                        (object)['code' => "SP", 'name' => 'São Paulo'],
                                        (object)['code' => "SE", 'name' => 'Sergipe'],
                                        (object)['code' => "TO", 'name' => 'Tocantins']
                                    ];
                                @endphp
                                <div class="form-group">
                                    <label>Estado</label>
                                    {{-- {{dd($provice_codes)}} --}}
                                    <select name="province_code" class='form-control' >                                        
                                        @foreach ($provice_codes as $state)
                                            <option value="{{$state->code}}" {{ $state->code == $order->customer->address->province_code ? 'selected' : '' }}>{{$state->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="float-right mt-4">
                            <button type='submit' class="btn btn-success">Gerar nova Fatura</button>
                        </div>
                    </form>
                </div>
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
