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
                            <h3 class="mb-0">Assinatura Lojista Pagas</h3>
                        </div>
                        <div class="col">
                            <div class="float-right">
                              
                            </div>
                        </div>
                    </div>
                </div>
               
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Shop ID</th>
                                <th scope="col">Plano</th>
                                <th scope="col">Data Pagamento</th>
                                <th scope="col">Valor</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assinaturashop as $shop)
                            
                            <tr>
                                <th scope="row">
                                    {{ $shop->id }} 
                                </th>
                                <td>
                                    {{ $shop->plan }}
                                </td>
                                <td>
                                {{ $shop->date_payment }}
                                </td>
                                <td>
                                {{ $shop->total }}
                                </td>
                               
                            </tr>
                            @empty
                            <tr>
                                <th scope="row" colspan="6">
                                Nenhuma Assinatura Paga Esse Mês.
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
                            <h3 class="mb-0">Assinatura Fornecedor Pagas</h3>
                        </div>
                        <div class="col">
                            <div class="float-right">
                              
                            </div>
                        </div>
                    </div>
                </div>
               
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Fornecedor ID</th>
                                <th scope="col">Plano</th>
                                <th scope="col">Data Pagamento</th>
                                <th scope="col">Valor</th>
 
                               
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($assinaturafor as $forn)
                            
                            <tr>
                                <th scope="row">
                                    {{ $forn->id }} 
                                </th>
                                <td>
                                    {{ $forn->plan }}
                                </td>
                                <td>
                                {{ $forn->date_payment }}
                                </td>
                                <td>
                                {{ $forn->total }}
                                </td>
                               
                            </tr>
                            @empty
                            <tr>
                                <th scope="row" colspan="6">
                                    Nenhuma Assinatura Paga Esse Mês.
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-danger">Excluir</button>
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