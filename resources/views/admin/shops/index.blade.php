@extends('admin.layout.default')

@section('title', config('app.name'))

@section('content')
<div class="modal fade" role="dialog" tabindex="-1" id="modal-delete-card">
    <div class="modal-dialog" role="document">
        <form action="" method="POST" id="form-delete-card">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Excluir registro</h3>
                </div>
                <div class="modal-body">
                    <p>Você tem certeza que deseja excluir este registro?</p>
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
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-4 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Lojistas cadastrados</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ $shops->count() }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-store"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Pedidos pendentes</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ $orderPending }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                        <i class="fas fa-box"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Pedidos pagos</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ $orderPaid }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-shipping-fast"></i>
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
                            <h2 class="mb-0">Lojistas</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 pt-4">
                        <form method="GET" action="{{route('admin.shops.search')}}">
                            <div class="ml-4">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">                                
                                            <input class="form-control" type="search" name='query' placeholder='Buscar pelo Nome, E-mail ou telefone' id="search-query" value={{request()->query('query') ? request()->query('query') : ''}}>
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
                        </form>
                    </div>
                </div>
				<div class="table-responsive">
                
                    <table class="table table-flush align-items-center border-bottom">
                        <thead>
                            <tr>
                                <th>Dados</th>
                                <th>Assinatura</th>
                                <th style="width:50px" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $dataAtual = date("Y-m-d");
                                $arrStatusInternalSubscription = ['active' => 'Ativa', 'inactive' => 'Inativa', 'overdue' => 'Atrasada', 'pending' => 'Pendente']; //status
                            @endphp
                            @forelse($shops as $shop)
                                <tr style="font-size:5px">
                                    <td>Nome         : {{ $shop->name }} <br>
                                        Email        :{{ $shop->email }}<br>
                                        Telelefone   :{{ $shop->phone }}  <br>
                                        Cadastrado em: {{ date('d/m/Y', strtotime($shop->created_at)) }} 
                                    </td>
                                   
                                                                       
                                    <td>
                                   
                                    @if(isset($shop->contracted_plan))
                                    @if(($shop->contracted_plan->name_plan == 'FREE') and ($shop->contracted_plan->subscription_status == 'active'))
                                        Gratuito
                                    @endif    

                                    @if(($shop->contracted_plan->name_plan <> 'FREE') and ($shop->contracted_plan->subscription_status == 'active'))
                                     Ativa
                                    
                                    @elseif(($shop->contracted_plan->name_plan <> 'FREE') and ($shop->contracted_plan->subscription_status == 'inactive'))
                                     Sem Acesso
                                    
                                    @elseif(($shop->contracted_plan->name_plan <> 'FREE') and ($shop->contracted_plan->subscription_status == 'atrasada'))
                                     Atrasada
                                    
                                    @elseif(($shop->contracted_plan->name_plan <> 'FREE') and ($shop->contracted_plan->subscription_status == 'pending'))
                                     Pendente
                                    
                                    
                                    @elseif(($shop->contracted_plan->name_plan == 'FREE') and ($shop->contracted_plan->subscription_status == 'active'))
                                        Gratuito
                                    @endif 
                                    @else 

                                    Sem Plano
                                    @endif            
                                           
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.shops.show', $shop->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="Detalhes">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                        @if($shop->token_card)
                                            <a href="#!" data-toggle="modal" data-target="#modal-delete-card" onclick="updateDeleteModalCard('{{ route('admin.shops.delete.card', $shop) }}')" class="btn btn-danger btn-sm" tooltip="true" title="Excluir Cartão lojista" target="_blank">
                                                <i class="fas fa-fw fa-credit-card"></i>
                                            </a>
                                        @endif
                                        @if($shop->status == 'active')
                                            <a href="{{ route('admin.shops.toggle_status', $shop->id) }}" class="btn btn-success btn-sm" tooltip="true" title="Pagamento da assinatura confirmado">
                                                <i class="fas fa-fw fa-check"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.shops.toggle_status', $shop->id) }}" class="btn btn-danger btn-sm" tooltip="true" title="Pagamento da assinatura pendente">
                                                <i class="fas fa-fw fa-times"></i>
                                            </a>
                                        @endif
                                        @if($shop->login_status == 'authorized')
                                            <a href="{{ route('admin.shops.toggle_login', $shop->id) }}" class="btn btn-success btn-sm" tooltip="true" title="Login autorizado">
                                                <i class="fas fa-fw fa-check-circle"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.shops.toggle_login', $shop->id) }}" class="btn btn-danger btn-sm" tooltip="true" title="Login não autorizado">
                                                <i class="fas fa-fw fa-times-circle"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.shops.more_days_free', $shop->id) }}" class="btn btn-success btn-sm" tooltip="true" title="+ 7 dias grátis">
                                            <i class="fas fa-fw fa-plus"></i>
                                        </a>
                                        <a href="{{ route('admin.shops.login', $shop->id) }}" class="btn btn-info btn-sm" tooltip="true" title="Logar no painel do lojista" target="_blank">
                                            <i class="fas fa-fw fa-sign-in-alt"></i>
                                        </a>
                                        <!-- <a href="#!" data-toggle="modal" data-target="#modal-delete" onclick="updateDeleteModal('{{ route('admin.shops.delete', $shop->id) }}')" class="btn btn-danger btn-sm" tooltip="true" title="Excluir lojista" target="_blank">
                                            <i class="fas fa-fw fa-times"></i>
                                        </a> -->                                        
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Nenhum lojista cadastrado no momento.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        
                    </table>
                    
                    {!! $shops->appends(request()->query())->render() !!}
                </div>
    		</div>
    	</div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function updateDeleteModalCard(route){
            $("#form-delete-card").attr('action', route);
        }

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

            // });
        } );
    </script>
@endsection
