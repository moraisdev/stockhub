@extends('shop.layout.default')

@section('title', config('app.name').' - Pagamentos')

@section('content')


    <div class="modal fade" role="dialog" tabindex="-1" id="modal-delete-order-in-group">
        <div class="modal-dialog" role="document">
            <form action="" method="POST" id="form-delete-order-in-group">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Excluir Pedido</h3>
                    </div>
                    <div class="modal-body">
                        <p>Você tem certeza que deseja excluir o pedido <b><span id='name-order-delete'></span></b> desta fatura?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-danger">Excluir</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Header -->
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">

            </div>
        </div>
    </div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-xl-4">
                                        <h2 class="mb-0 d-inline-block">Detalhes da fatura</h2>
                                    </div>
                                    <div class="col-xl-8">
                                         @if(($group->status != 'paid') and ($admins->pg_boleto == 0))
                                            <a href="{{ route('shop.orders.groups.pay', [ 'group_id' => $group->id, 'payment_method' => 'boleto']) }}" class="btn btn-success float-right">Gerar boleto</a>
                                         @endif 
                                         @if(($group->status != 'paid') and ($admins->pg_pix == 0))
                                        <a href="{{ route('shop.orders.groups.pay', [ 'group_id' => $group->id, 'payment_method' => 'pix']) }}" class="btn btn-default float-right">Pagar com Pix</a>
                                         @endif

                                         @if($group->status != 'paid')
                                        <form action="{{ route('shop.orders.groups.apply_discount', $group->id) }}" class="float-right mr-4" method="POST">
                                            {{ csrf_field() }}
                                            <div class="form-group mb-0">
                                                <div class="input-group">
                                                    <input type="text" name="code" class="form-control" placeholder="Código de desconto">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-success">Aplicar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        @endif
                                      


                             
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   

                    


                    <div class="card-body">
                        <div class="row">
                            @if(count($group->discounts) > 0)
                                <div class="col-12">
                                    <h3>Cupons aplicados neste pedido</h3>
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>Cupom</th>
                                                <th>Produto</th>
                                                <th>Desconto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($group->discounts as $disc)
                                                <tr>
                                                    <td>{{ $disc->discount->code }}</td>
                                                    @if(strtoupper(substr($disc->discount->code, -3)) == 'ALL')
                                                        <td>Aplicado a todos os itens</td>
                                                    @else   
                                                        <td>{{ $disc->discount->variant->title }}</td>
                                                    @endif
                                                    
                                                    <td>{{ $disc->discount->percentage }}%</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">Nenhum cupom utilizado neste pedido</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <hr>
                                </div>
                            @endif
                            
                            @if($group->transaction_id_pix && $group->qrcode_pix)
                            <div class='col-lg-3 mb-3'>                                
                                <img src="{{$group->qrcode_pix}}" alt="qrcode do pix" style='max-width: 593px; width: 100%;'>
                            </div>
                            @endif

                            <div class="col-lg-9">
                                {{-- @if($group->transaction_id_pix && $group->qrcode_pix)
                                    <h2>Valor Final: <b>R$ {{ number_format(round($group->orders->sum('total_amount') * 1.01, 2) + 0.01 , 2, ',','.') }}</b></h2>
                                @endif --}}
                                <p style='margin-bottom: 0;'>Valor: <b>R$ {{ number_format($group->orders->sum('total_amount'), 2, ',','.') }}</b></p>
                                @if($group->transaction_id_pix && $group->qrcode_pix)
                                    <small>Taxa de processamento Pix R$ {{ number_format(round($group->orders->sum('total_amount') * 0.01, 2) + 0.01 , 2, ',','.') }}</small>
                                @endif
                                {{-- <p>Taxa de Processamento: <b>R$ 1,80</b></p>
                                <p>Valor final: <b>R$ {{ number_format($group->orders->sum('total_amount') + 1.8, 2, ',','.') }}</b></p> --}}
                                <p class='mt-3'>Status do pagamento: <span class="badge badge-warning">{{ $group->status }}</span></p>
                                <small>Ao efetuar todos os pagamentos para os fornecedores o status do grupo será quitado.</small>
                                

                                @if($group->transaction_id_pix && $group->key_pix)
                                {{-- <div class='col-lg-6'> --}}
                                    <h4 class='mt-5'>Código do Pix <button type="button" id='copy-pix-key' class="btn btn-primary btn-sm ml-3"><i class='ni ni-single-copy-04'></i> Copiar Código</button></h4>
                                    <textarea id='key-pix' class='form-control' readonly style='font-size: 10pt; font-weight: bold; padding: 5px; border: 1px solid #cecece;'>{{$group->key_pix}}</textarea>                                    
                                    <small>{{$group->description_pix}}</small><br />
                                    <small><span style="color: #fa3d3d;">*</span> Caso passe muito tempo e o QRCode esteja indisponível, clique em "Pagar com Pix" novamente para gerar outro.</small><br>
                                    <small><span style="color: #fa3d3d;">**</span> Devido ao sistema pix ser uma funcionalidade nova, ela não funciona de forma correta em todos os bancos, segue a lista de bancos onde a nossa integração do pix com a safe2pay funciona corretamente, caso seu banco não esteja nessa lista, é melhor escolher outra forma de pagamento para evitar transtornos. <i>Lista de bancos: Ame Digital, Banco do Brasil, Bradesco, BS2, Caixa, Caixa Tem, Gerencianet, Juno, NuBank e PicPay</i></small>
                                {{-- </div> --}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($group->transaction_id_pix)
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">Você gerou um pix para esta fatura</h2>
                                <small>Confira os dados do pix gerado abaixo</small>
                            </div>
                        </div>
                    </div>
                    {{-- {{dd($group->discounts_returneds)}} --}}
                    @php
                        //caso tenha algum desconto por reembolso, contabiliza
                        if(count($group->discounts_returneds) > 0){
                            $finalValueTicket = round($group->orders->sum('total_amount') * 1.01, 2) + 0.01 + $group->discounts_returneds->sum('amount');
                            //$finalValueTicket = $group->orders->sum('total_amount') + 1.8 + $group->discounts_returneds->sum('amount');
                        }else{
                            $finalValueTicket = round($group->orders->sum('total_amount') * 1.01, 2) + 0.01;
                        }
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                                <tr>
                                    <th>Taxa</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Taxa Processamento Gateway (1% pix)</td>
                                    <td>+ R$ {{ number_format(round($group->orders->sum('total_amount') * 0.01, 2) + 0.01 , 2, ',','.') }}</td>
                                </tr>
                                @if(count($group->discounts_returneds) > 0)
                                   @foreach ($group->discounts_returneds as $discount_returned)
                                    <tr>
                                        <td>Desconto Reembolso Pedido {{$discount_returned->order_returned->order->name}}</td>
                                        <td><span style='color: #f5365c'>- R$ {{ number_format(-1*$discount_returned->amount, 2, ',','.') }}</span></td>
                                    </tr>
                                   @endforeach 
                                @endif
                                <tr>
                                    <td>Valor do Pedido</td>
                                    <td>+ R$ {{ number_format($group->orders->sum('total_amount'), 2, ',','.') }}</td>
                                </tr>

                                <tr>
                                    <td><b>Valor Final Pix</b></td>
                                    <td><b>R$ {{ number_format($finalValueTicket, 2, ',','.') }}</b></td>
                                </tr>                                    
                            </tbody>
                        </table>                            
                    </div>
                </div>
            @endif

                @if($group->transaction_id && $group->bankslip_url)
                    <div class="card shadow mt-4">
                        <div class="card-header bg-transparent">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h2 class="mb-0">Você gerou um boleto para esta fatura</h2>
                                    <small>Confira os dados do boleto gerado abaixo</small>
                                </div>
                            </div>
                        </div>
                        {{-- {{dd($group->discounts_returneds)}} --}}
                        @php
                            //caso tenha algum desconto por reembolso, contabiliza
                            if(count($group->discounts_returneds) > 0){
                                $finalValueTicket = $group->orders->sum('total_amount') + 3.45 + $group->discounts_returneds->sum('amount');
                            }else{
                                $finalValueTicket = $group->orders->sum('total_amount') + 3.45;
                            }
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-flush align-items-center">
                                <thead>
                                    <tr>
                                        <th>Taxa</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Taxa Processamento Gateway</td>
                                        <td>+ R$ 3,45</td>
                                    </tr>
                                    @if(count($group->discounts_returneds) > 0)
                                       @foreach ($group->discounts_returneds as $discount_returned)
                                        <tr>
                                            <td>Desconto Reembolso Pedido {{$discount_returned->order_returned->order->name}}</td>
                                            <td><span style='color: #f5365c'>- R$ {{ number_format(-1*$discount_returned->amount, 2, ',','.') }}</span></td>
                                        </tr>
                                       @endforeach 
                                    @endif
                                    <tr>
                                        <td>Valor do Pedido</td>
                                        <td>+ R$ {{ number_format($group->orders->sum('total_amount'), 2, ',','.') }}</td>
                                    </tr>

                                    <tr>
                                        <td><b>Valor Final Boleto</b></td>
                                        <td><b>R$ {{ number_format($finalValueTicket, 2, ',','.') }}</b></td>
                                    </tr>                                    
                                </tbody>
                            </table>                            
                        </div>
                        <hr>
                        <div class="row d-flex justify-content-center pb-4">
                            {{-- <div class="col-md-12 text-center">
                                <h4>Data de vencimento <b>{{ $group->bankslip_duedate && $group->bankslip_duedate != '1970-01-01' ? date('d/m/Y', strtotime($group->bankslip_duedate)) : 'Indisponível' }}</b></h4>
                            </div>                             --}}
                            <div class="col-md-12 text-center">
                                <a href="{{ $group->bankslip_url }}" target="_blank" class="btn btn-info">Imprimir Boleto</a>
                            </div>
                        </div>
                        
                        
                    </div>
                @endif

                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">Pedidos deste pagamento</h2>
                                <small>Nota: Os pedidos listados abaixo são os <b><i>pedidos para o fornecedor</i></b>, se você possui um <b><i>pedido da sua loja</i></b> que contém produtos de mais de um fornecedor, será gerado abaixo um pedido para cada fornecedor.</small>
                            </div>
                        </div>
                        <small id="idgroup">{{$group->id}}</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Produtos</th>
                                <th>Valor em Produtos</th>
                                <th>Frete</th>
                                <th>Total</th>
                                <th>Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalProdutos = 0.0;    
                                    $totalFretes = 0.0;
                                    $totalFinal = 0.0;
                                @endphp
                                 @forelse($group->orders as $order)
                                    <tr>
                                        <td><a href="{{ route('shop.orders.show', [$order->order->id]) }}" target="_blank">{{ $order->order->name }}</a></td>
                                        @if($order->order->customer)
                                            <td>{{ $order->order->customer->first_name }} {{ $order->order->customer->last_name }}</td>
                                        @else
                                            <td>Indisponível</td>
                                        @endif
                                        <td>
                                            <div class="avatar-group">
                                                @foreach($order->items as $item)
                                                    @if($item->variant)
                                                        <a href="#" class="avatar avatar-sm" tooltip="true" title="{{ $item->quantity.'x '.$item->variant->title }}">
                                                            <img alt="{{ $item->variant->title }}" src="{{ ($item->variant->img_source) ? $item->variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="rounded-circle bg-white w-100 h-100">
                                                        </a>
                                                    @else
                                                        @php
                                                            $variant = \App\Models\ProductVariants::withTrashed()->find($item->product_variant_id);
                                                        @endphp

                                                        @if($variant)
                                                            O produto <b>{{$variant->title }}</b> não está mais disponível
                                                        @endif
                                                        
                                                    @endif
                                                    
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>R$ {{ number_format($order->amount,2,',','.') }}</td>
                                        <td>R$ {{ number_format($order->total_amount - $order->amount,2,',','.') }}</td>
                                        @if($order->external_service == 'woocommerce')
                                        <td>R$ {{ number_format($order->total_amount + $order->shipping_amount, 2, ',', ',') }}</td>
                                            @php
                                                $totalFinal += $order->total_amount + $order->shipping_amount;
                                            @endphp
                                        @else
                                        <td>R$ {{ number_format($order->total_amount, 2, ',', ',') }}</td>
                                            @php
                                                $totalFinal += $order->total_amount;
                                            @endphp
                                        @endif
                                        <td>
                                            @if($order->status == 'pending' && $order->order->status == 'pending')
                                                {{-- Caso esteja pendente, ainda tem a possibilidade de excluir --}}
                                                <a href="#!" data-toggle='modal' data-target='#modal-delete-order-in-group' onclick="updateDeleteModalOrderInGroup('{{$order->order->name}}', '{{route('shop.orders.groups.order.delete', ['group_id' => $group->id, 'order_id' => $order->order->id])}}')" class="btn btn-danger btn-sm" tooltip="true" title="Excluir">
                                                    <i class="fas fa-fw fa-times"></i>
                                                </a>
                                            @endif
                                        </td>
                                        @php
                                            $totalProdutos += $order->amount;
                                            $totalFretes += ($order->total_amount - $order->amount);
                                        @endphp
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">Nenhum pedido neste pagamento</td>
                                    </tr>
                                @endforelse
                                <tr>
                                    <th colspan="3" class="text-right">Total</th>
                                    <td>R$ {{number_format($totalProdutos,2,',','.')}}</td>
                                    <td>R$ {{number_format($totalFretes,2,',','.')}}</td>
                                    <td>R$ {{number_format($totalFinal,2,',','.')}}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

      
@endsection

@section('scripts')
    <script>
        function updateDeleteModalOrderInGroup(name, route){
            $('#name-order-delete').html(name)
            $("#form-delete-order-in-group").attr('action', route);
        }
    </script>

<script>

function atualizar()
{
    var data = $(this).serialize();
    var grupo = document.getElementById('idgroup').innerText;
    var _token = $('meta[name="_token"]').attr('content');
    $.ajaxSetup({

    headers: {

    'X-CSRF-TOKEN': _token
    }
    });

   
    $.ajax({
    url:"{{ route('shop.consultapixger')}}",
    type:"get",
    datatType : 'json',
    data: { grupo: grupo } ,
    success:function(data){
      //    console.log(data);
	var retorno = JSON.parse(data);
		 
		// console.log(retorno.dados)
        
		  
		if (retorno.dados == 'paid' ){
            location.reload()

          }
    //alert(data);
    },     
    error: function( data )
            {
                if(!data.responseJSON){
                    console.log(data.responseText);
                    $('#err').html(data.responseText);
                }else{
                    $('#err').html('');
                    $.each(data.responseJSON.errors, function (key, value) {
                        $('#err').append(key+": "+value+"<br>");
                        console.log(key);
                    });
                }
            }
        });
    
} 




setInterval("atualizar()", 10000);
</script>



@endsection