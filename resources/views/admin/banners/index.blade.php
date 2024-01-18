@extends('admin.layout.default')

@section('title', config('app.name'))

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
    <div class="row">
        <div class="col-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                        <h3 class="mb-0">Banners</h3>
                        </div>
                        <div class="col">
                            <div class="float-right">
                                {{--<a class="btn btn-success" href="{{ route('admin.banners.import.csv_instructions') }}"><i class="fas fa-file-csv mr-2"></i> Importar CSV</a>--}}
                            <a class="btn btn-primary" href="{{ route('admin.banners.create') }}"><i class="fas fa-plus mr-2"></i> Novo Banner </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">Link</th>
                                <th scope="col">Imagem</th>
                                <th scope="col">Atualizado em</th>
                                <th scope="col" class="actions-th">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banners as $banner)
                            <?php
                                $delete_action = route('admin.banners.destroy', $banner->id);
                            ?>
                            <tr>
                                <td>
                                    {{ $banner->name }}
                                </td>
                                <td>
                                    {{ $banner->link ? $banner->link : 'N/A' }}
                                </td>
                                <td>
                                    {{ date('d/m/Y H:i:s', strtotime($banner->updated_at)) }}
                                </td>
                                <td class="actions-td">
                                    <a href="{{ route('admin.banners.show', $banner->id) }}" class="btn btn-primary btn-circle" role='button'><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-info btn-circle" role='button'><i class="fas fa-pencil-alt"></i></a>
                                    <a href="#!" data-toggle="modal" data-target="#delete_modal" onclick="update_delete_form_action('{{ $delete_action }}')" class="btn btn-danger btn-circle" role='button'><i class="fas fa-times"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <th scope="row" colspan="6">
                                    Nenhum banner cadastrado
                                </th>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-4">
                    <div class="float-right">
                        {{ $banners->render() }}
                    </div>
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
                        <h5 class="modal-title">Apagar Banner</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja apagar o banner?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-danger">Apagar</button>
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
