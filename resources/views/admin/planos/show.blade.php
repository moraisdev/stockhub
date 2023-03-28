@extends('admin.layout.default')

@section('title', 'Categorias')

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
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-4 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total de categorias</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Categoria mais popular</h5>
                                    <span class="h2 font-weight-bold mb-0" style="font-size: 1rem">
                                        Indisponível
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                        <i class="fas fa-star"></i>
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Vendas da categoria<sup><i class="far fa-star"></i></sup></h5>
                                    <span class="h2 font-weight-bold mb-0">
                                        Indisponível
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-arrow-up"></i>
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
        <div class="col-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Categorias</h3>
                        </div>
                        <div class="col">
                            <div class="float-right">
                                <a class="btn btn-primary" href="{{ route('admin.categories.create') }}"><i class="fas fa-plus mr-2"></i> Nova categoria</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body my-0">
                    <p class="my-0">Listagem de categorias do sistema. Estas categorias estarão disponíveis para os fornecedores no cadastro de produtos.</p>
                </div>
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nome</th>
                                <th scope="col" class="actions-th">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <?php 
                                $delete_action = route('admin.categories.destroy', $category->id);
                            ?>
                            <tr>
                                <th scope="row">
                                    #{{ $category->id }}
                                </th>
                                <td>
                                    {{ $category->name }}
                                </td>
                                <td class="actions-td">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-info btn-circle" role='button'><i class="fas fa-pencil-alt"></i></a>
                                    <a href="#!" data-toggle="modal" data-target="#delete_modal" onclick="update_delete_form_action('{{ $delete_action }}')" class="btn btn-danger btn-circle" role='button'><i class="fas fa-times"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <th scope="row" colspan="6">
                                    Nenhuma categoria cadastrada ainda.
                                </th>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="delete_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="" id="delete_form">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Excluir categoria</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Você tem certeza que deseja excluir esta categoria?</p>
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