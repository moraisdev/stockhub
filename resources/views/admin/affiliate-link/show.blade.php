@extends('admin.layout.default')

@section('title', config('app.name').' - Visualizar Link Afiliado')

@section('stylesheets')
<style type="text/css">
    .thumbnail{
        width: 100%;
        height: 250px;
        background-size: cover;
        background-position: center;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
            <h1 class="display-2 text-white">Visualizar Link Afiliado</h1>
                <a href="{{ route('admin.affiliate-link.index') }}" class="btn btn-secondary">{{ __('supplier.back') }}</a>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-2">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Link Afiliado</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="heading-small text-muted mb-4">Informações do Link Afiliado</h6>
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="product_title">Nome Afiliado</label>
                                        <input type="text" id="product_title" class="form-control form-control-alternative" name="name" placeholder="Nome do Afiliado" value="{{ $link->name }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Tipo de Link</label>
                                        <select class="form-control" id="type" name='type' disabled>
                                            <option value='shop' @if($link->type == 'shop') {{'selected'}} @endif>Lojista</option>
                                            <option value='supplier' @if($link->type == 'supplier') {{'selected'}} @endif>Fornecedor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h3>Ativos no momento: <b>{{$countAtivos}}</b></h3>
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">E-mail</th>
                                <th scope="col">Situação</th>
                                <th scope="col">Data Cadastro</th>
                                <th style="width:50px" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shops as $shop)
                            <tr>
                                <td>
                                    {{ $shop->name }}
                                </td>
                                <td>
                                    {{ $shop->email }}
                                </td>
                                <td>
                                    {{ $shop->status }}
                                </td>
                                <td>
                                    {{ date('d/m/Y H:i:s', strtotime($shop->created_at)) }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.shops.login', $shop->id) }}" class="btn btn-info btn-sm" tooltip="true" title="Logar no painel do lojista" target="_blank">
                                        <i class="fas fa-fw fa-sign-in-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <th scope="row" colspan="6">
                                    Nenhum lojista cadastrado ainda
                                </th>
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
