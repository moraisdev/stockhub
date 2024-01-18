@extends('supplier.layout.default')

@section('title', 'Visualizar pedido')

@section('content')
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Dash data</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Dash data</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
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
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Dash data</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-credit-card"></i>
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Dash data</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-percent"></i>
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
                            <h2 class="mb-0">Pedido {{ $supplier_order->f_display_id }}</h2>
                        </div>
                        <div class="d-flex col align-items-center justify-content-end">
                            <h2>Valor total: R$ {{ \App\Http\Controllers\Supplier\FunctionsController::supplierOrderAmount($supplier_order) }}</h2>
                        </div>
                    </div>
                </div>

                @if (env('DADOSLOJISTA') == '1')  
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h4>Detalhes do lojista:</h4>
                            <p class="mb-0">
                                <b>Loja:</b> {{ $supplier_order->order->shop->name }} <br>
                                <b>Nome do responsável:</b> {{ $supplier_order->order->shop->responsible_name }} <br>
                                <b>CPF/CNPJ:</b> {{ $supplier_order->order->shop->document }} <br>
                                @if(strlen($supplier_order->order->shop->f_document) > 14)
                                    <b>Nome fantasia:</b> {{ $supplier_order->order->shop->fantasy_name ? $supplier_order->order->shop->fantasy_name : 'não cadastrado' }} <br>
                                    <b>Razão Social:</b> {{ $supplier_order->order->shop->corporate_name ? $supplier_order->order->shop->corporate_name : 'não cadastrado' }} <br>
                                    <b>Inscrição Estadual:</b> {{ $supplier_order->order->shop->state_registration ? $supplier_order->order->shop->state_registration : 'não cadastrado' }} <br>
                                @endif
                            </p>
                        </div>
                        <div class="col d-flex justify-content-end align-items-center">
                            <a href="{{ route('supplier.partners.show', $supplier_order->order->shop->hash) }}" class="btn btn-info">Ver detalhes do lojista</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="card shadow mt-4">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">Detalhes do frete</h2>
                        </div>
                        @if($authenticated_user->shipping_method != 'melhor_envio')
                            <div class="d-flex col align-items-center justify-content-end">
                                <a href="{{ route('supplier.orders.print_content_declaration', $supplier_order->id) }}" target="_blank" class="btn btn-danger btn-sm" tooltip="true" title="Imprimir Declaração de Conteúdo">
                                    <i class="fas fa-fw fa-file"></i> Imprimir Declaração de Conteúdo
                                </a>
                                <a href="{{ route('supplier.orders.print_tag', $supplier_order->id) }}" target="_blank" class="btn btn-danger btn-sm" tooltip="true" title="Imprimir Etiqueta">
                                    <i class="fas fa-fw fa-tag"></i> Imprimir etiqueta
                                </a>
                            </div>
                        @endif
                        
                    </div>
                </div>
                <div class="card-body">
                    <h4>Detalhes do cliente:</h4>
                    <div class="row">
                        <div class="col">
                            <p>
                                <b>Nome:</b> {{ $supplier_order->order->customer->address->name }} <br>
                                <b>E-mail:</b> {{ $supplier_order->order->customer->email }} <br>
                                <b>Telefone:</b> {{ $supplier_order->order->customer->address->phone }} <br>
                            </p>
                        </div>
                        <div class="col">
                            <p>
                                <b>Endereço:</b> {{ $supplier_order->order->customer->address->address1 }} <br>
                                @if($supplier_order->order->customer->address->address2)
                                    {{ $supplier_order->order->customer->address->address2 }}<br>
                                @endif
                                @if($supplier_order->order->customer->address->company)
                                    {{ $supplier_order->order->customer->address->company }}<br>
                                @endif
                                <b>CEP:</b> {{ $supplier_order->order->customer->address->zipcode }} <br>
                                <b>Cidade:</b> {{ $supplier_order->order->customer->address->city }} <br>
                                <b>Estado:</b> {{ $supplier_order->order->customer->address->province }} <br>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @if($supplier_order->order->shipping_label)
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">Etiqueta</h2>
                                <a class='btn btn-primary mt-3' download='{{$supplier_order->f_display_id}}-etiqueta' style='color: #fff' href='{{asset('etiqueta/'. $supplier_order->order->shipping_label->url_labels)}}'><i class="fas fa-file-download"></i> Baixar etiqueta</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <form method="POST" action="{{ route('supplier.orders.update_comments', [$supplier_order->id]) }}">
                @csrf
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">Anotações</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-3">
                        <textarea name="comments" rows="6" class="form-control">{{ $supplier_order->comments }}</textarea>
                        <div class="float-right">
                            <button class="btn btn-primary mt-2">Salvar</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card shadow mt-4">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">Meus produtos neste pedido</h2>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th>Qtd</th>
                                <th>Produto</th>
                                <th>SKU</th>
                                <th>Valor unitário</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supplier_order->items as $item)
                                @if($item->variant)
                                    <tr>
                                        <td>{{ $item->quantity }}</td>
                                        <td><a href="{{ route('supplier.products.show', $item->variant->product_id) }}" target="_blank">{{ $item->variant->title }}</a></td>
                                        <td>{{ $item->variant->sku }}</td>
                                        <td>{{ $item->variant->product->currency == 'US$' ? 'R$ '.(number_format($item->variant->price * $dolar_price['price'],2,',','.')) : 'R$ '.number_format($item->variant->price,2,',','.') }} </td>
                                    </tr>
                                @else
                                    {{-- Busca o produto, mesmo excluído, só a nível de exibição --}}
                                    @php
                                        $variant = \App\Models\ProductVariants::withTrashed()->find($item->product_variant_id);
                                        if($variant){ //busca o produto
                                            $product = \App\Models\Products::withTrashed()->find($variant->product_id);
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $item->quantity }}</td>
                                        <td>O produto <b>{{$variant->title }}</b> não está mais disponível</td>
                                        <td>{{ $variant->sku }}</td>
                                        <td>{{ $product->currency == 'US$' ? 'R$ '.(number_format($variant->price * $dolar_price['price'],2,',','.')) : 'R$ '.number_format($variant->price,2,',','.') }} </td>
                                    </tr>
                                @endif
                                
                            @empty
                                <tr>
                                    <td colspan="6">Não há nenhum produto ligado à este pedido. Entre em contato com o suporte da {{config('app.name')}}.</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td style="font-size: 1rem" colspan="3" class="text-right">Valor total: </td>
                                <th style="font-size: 1rem">R$ {{ \App\Http\Controllers\Supplier\FunctionsController::supplierOrderAmount($supplier_order) }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
    		</div>
    	</div>
    </div>
</div>
@endsection
