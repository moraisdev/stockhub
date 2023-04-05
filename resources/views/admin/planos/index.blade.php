@extends('admin.layout.default')

@section('title', 'Planos de Assinatura')

@section('stylesheets')
<style type="text/css"> 
    .btn-circle {  
        padding: 7px 10px; 
        border-radius: 50%; 
        font-size: 1rem; 
    } 
</style> 
@endsection

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
<div class="container-fluid">
        <div class="header-body">
        </div>
    </div>
</div>

<div class="container-fluid mt--7">
  
@if ($admins->plano_shop <> 0 )
    <div class="row">
        <div class="col-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Planos de Assinatura Lojista</h3>
                        </div>
                        <div class="col">
                            <div class="float-right">
                              
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body my-0">
                    <p class="my-0">Listagem dos planos do sistema. Estes planos estarão disponíveis para os lojistas para assinatura.</p>
                </div>
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ trans('supplier.name') }}</th>
                                <th scope="col">{{ trans('supplier.price') }}</th>
                                <th scope="col">Ciclo</th>
                                <th scope="col">{{ trans('supplier.text_status') }}</th>
 
                                <th scope="col" class="actions-th">{{ trans('supplier.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($planos as $plano)
                            
                            <tr>
                                <th scope="row">
                                    {{ $plano->id }}
                                </th>
                                <td>
                                    {{ $plano->titulo }}
                                </td>
                                <td>
                                    {{ $plano->valor }}
                                </td>
                                <td>
                                    {{ $plano->ciclo }}
                                </td>
                                @if ($plano->status == 0)
                                <td>
                                    DESATIVADO
                                </td>
                                @endif
                                @if ($plano->status == 1)
                                <td>
                                    {{ trans('supplier.active') }}
                                </td>
                                @endif
                                <td class="actions-td">
                                 
                                 
                                <a href="{{ route('admin.planos.edit', $plano->id) }}" class="btn btn-info btn-circle" role='button'><i class="fas fa-pencil-alt"></i></a>
                                <a href="#!" data-toggle="modal" data-target="#delete_modal" onclick="" class="btn btn-danger btn-circle" role='button'><i class="fas fa-times"></i></a>
           
                                  </td>
                            </tr>
                            @empty
                            <tr>
                                <th scope="row" colspan="6">
                                    Nenhuma plano cadastrado ainda.
                                </th>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
    <br>

    @if ($admins->plano_f <> 0 )
    <div class="row">
        <div class="col-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Planos de Assinatura Fornecedor</h3>
                        </div>
                        <div class="col">
                            <div class="float-right">
                              
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body my-0">
                    <p class="my-0">Listagem dos planos do sistema. Estes planos estarão disponíveis para os fornecedores para assinatura.</p>
                </div>
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ trans('supplier.name') }}</th>
                                <th scope="col">{{ trans('supplier.price') }}</th>
                                <th scope="col">Ciclo</th>
                                <th scope="col">{{ trans('supplier.text_status') }}</th>
 
                                <th scope="col" class="actions-th">{{ trans('supplier.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($planosforn as $planofor)
                            
                            <tr>
                                <th scope="row">
                                    {{ $planofor->id }}
                                </th>
                                <td>
                                    {{ $planofor->titulo }}
                                </td>
                                <td>
                                    {{ $planofor->valor }}
                                </td>
                                <td>
                                    {{ $planofor->ciclo }}
                                </td>
                                @if ($planofor->status == 0)
                                <td>
                                    DESATIVADO
                                </td>
                                @endif
                                @if ($planofor->status == 1)
                                <td>
                                    {{ trans('supplier.active') }}
                                </td>
                                @endif
                                <td class="actions-td">
                                 
                                 
                                <a href="{{ route('admin.planosf.edit', $planofor->id) }}" class="btn btn-info btn-circle" role='button'><i class="fas fa-pencil-alt"></i></a>
                                <a href="#!" data-toggle="modal" data-target="#delete_modal" onclick="" class="btn btn-danger btn-circle" role='button'><i class="fas fa-times"></i></a>
           
                                  </td>
                            </tr>
                            @empty
                            <tr>
                                <th scope="row" colspan="6">
                                    Nenhuma plano cadastrado ainda.
                                </th>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="modal fade" tabindex="-1" role="dialog" id="delete_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="" id="delete_form">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Excluir plano</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Você tem certeza que deseja excluir este plano?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('supplier.cancel') }}</button>
                        <button class="btn btn-danger">{{ trans('supplier.delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script type="text/javascript">
        function update_delete_form_action(action){
            $("#delete_form").attr('action', action);
        }
    </script>
@endsection