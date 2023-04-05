@extends('admin.layout.default')

@section('title', config('app.name'))

@section('content')
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Fornec. cadastrados</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ $suppliers->count() }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-user"></i>
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Pagamentos pendentes</h5>
                                    <span class="h2 font-weight-bold mb-0">
                                        R$ {{ number_format($supplierOrdersPending, 2, ',','.') }}
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                        <i class="fas fa-credit-card"></i>
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Pagamentos realizados</h5>
                                    <span class="h2 font-weight-bold mb-0">
                                    R$ {{ number_format($supplierOrdersPaid, 2, ',','.') }}
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
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
                            <h2 class="mb-0">{{ trans('supplier.fornecedores') }}</h2>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                    <table class="table table-flush align-items-center border-bottom">
                        <thead>
                            <tr>
                                <th>Dados</th>
                               
                                <th style="width:50px" class="text-center">{{ trans('supplier.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr>
                                    <td>{{ trans('supplier.name') }}: {{ $supplier->name }}<br>
                                        Nome Legal:{{ $supplier->legal_name }}<br>
                                        {{ trans('supplier.text_email') }} :{{ $supplier->email }}<br>
                                        {{ trans('supplier.text_phone') }}:  {{ $supplier->phone }}
                                        {{ trans('supplier.data_cadastro') }}: {{ date('d/m/Y', strtotime($supplier->created_at)) }}
                                    </td>
                                  
                                    
                                    <td><span class="d-none">{{ date('Ymd', strtotime($supplier->created_at)) }}</span>{{ date('d/m/Y', strtotime($supplier->created_at)) }}</td>
                                    <td>
                                        <a href="{{ route('admin.suppliers.show', $supplier->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="{{ trans('supplier.details') }}">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.import', $supplier->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="Cadastro rapido de produtos">
                                            <i class="fas fa-fw fa-boxes"></i>
                                        </a>
                                        @if($supplier->status == 'active')
                                            <a href="{{ route('admin.suppliers.toggle_status', $supplier->id) }}" class="btn btn-success btn-sm" tooltip="true" title="Atividades em andamento">
                                                <i class="fas fa-fw fa-check"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.suppliers.toggle_status', $supplier->id) }}" class="btn btn-danger btn-sm" tooltip="true" title="Pausa nas atividades">
                                                <i class="fas fa-fw fa-times"></i>
                                            </a>
                                        @endif
                                        @if($supplier->login_status == 'authorized')
                                            <a href="{{ route('admin.suppliers.toggle_login', $supplier->id) }}" class="btn btn-success btn-sm" tooltip="true" title="Login autorizado">
                                                <i class="fas fa-fw fa-check-circle"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.suppliers.toggle_login', $supplier->id) }}" class="btn btn-danger btn-sm" tooltip="true" title="Login não autorizado">
                                                <i class="fas fa-fw fa-times-circle"></i>
                                            </a>
                                        @endif
                                        @if($supplier->use_shipment_address == 1)
                                            <a href="{{ route('admin.suppliers.toggle_shipment_address', $supplier->id) }}" class="btn btn-success btn-sm" tooltip="true" title="Endereço de remessa ativado">
                                                <i class="fas fa-fw fa-truck"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.suppliers.toggle_shipment_address', $supplier->id) }}" class="btn btn-danger btn-sm" tooltip="true" title="Endereço de remesssa desativado">
                                                <i class="fas fa-fw fa-truck"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.suppliers.login', $supplier->id) }}" class="btn btn-info btn-sm" tooltip="true" title="Logar no painel do fornecedor" target="_blank">
                                            <i class="fas fa-fw fa-sign-in-alt"></i>
                                        </a>
                                        <!-- <a href="#!" data-toggle="modal" data-target="#modal-delete" onclick="updateDeleteModal('{{ route('admin.suppliers.delete', $supplier->id) }}')" class="btn btn-danger btn-sm" tooltip="true" title="Excluir lojista" target="_blank">
                                            <i class="fas fa-fw fa-times"></i>
                                        </a> -->
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Nenhum fornecedor cadastrado no momento.</td>
                                </tr>
                            @endforelse
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

            });
        } );
    </script>
@endsection
