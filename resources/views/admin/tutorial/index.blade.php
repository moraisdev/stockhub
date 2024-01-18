@extends('admin.layout.default')

@section('title', 'Tutoriais')

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
                        <h3 class="mb-0">Links do Tutorial</h3>
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
                                <th scope="col">Descrição</th>
                                <th scope="col">Link</th>
                                <th scope="col" class="actions-th">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tutorial as $tuto)
                            
                            <tr>
                                <td>
                                    {{ $tuto->descricao }}
                                </td>
                               
                                <td>{{$tuto->link}}</td>
                               
                                <td class="actions-td">
                                      <a href="{{ route('admin.tutorial.edit', $tuto->id) }}" class="btn btn-info btn-circle" role='button'><i class="fas fa-pencil-alt"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                               
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-4">
                    <div class="float-right">
                        
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
                        <h5 class="modal-title">Apagar link</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja apagar o link?</p>
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
