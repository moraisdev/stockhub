@extends('shop.layout.default')

@section('title', config('app.name').' - '.$title)

@section('content')

    <div class="modal fade" role="dialog" tabindex="-1" id="modal-delete-group">
        <div class="modal-dialog" role="document">
            <form action="" method="POST" id="form-delete-group">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Excluir Fatura</h3>
                    </div>
                    <div class="modal-body">
                        <p>Você tem certeza que deseja excluir a fatura pendente <b><span id='id-group-delete'></span></b>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('supplier.cancel') }}</button>
                        <button class="btn btn-danger">{{ trans('supplier.delete') }}</button>
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
                                <h2 class="mb-0">{{ $title }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center">
                            <p>
                                Listagem de faturas geradas na tela de pedidos, clique para obter os detalhes da fatura.<br>
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>{{ trans('supplier.actions') }}</th>
                                <th>ID Fatura</th>
                                <th>{{ trans('supplier.date') }}</th>
                                <th>{{ trans('supplier.text_status') }}</th>
                                <th>Pedidos Loja</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($groups as $group)
                                    <tr>
                                        <td>
                                            <a href="{{ route('shop.orders.group_detail', $group->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="{{ trans('supplier.details') }}">
                                                <i class="fas fa-fw fa-eye"></i>
                                            </a>
                                            @if($group->status == 'pending')
                                                <a href="#!" data-toggle='modal' data-target='#modal-delete-group' onclick="updateDeleteModalGroup({{$group->id}}, '{{route('shop.orders.groups.delete', ['group_id' => $group->id])}}')" class="btn btn-danger btn-sm" tooltip="true" title="{{ trans('supplier.delete') }}">
                                                    <i class="fas fa-fw fa-times"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{$group->id}}</td>
                                        <td><span class="d-none">{{ date('Y-m-d', strtotime($group->created_at)) }}</span>{{ date('d/m/Y', strtotime($group->created_at)) }}</td>
                                        <td>{{ \App\Services\Shop\OrdersService::translateStatus($group->status) }}</td>
                                        <td>
                                            @foreach($group->orders as $o)
                                                @if($o->order)
                                                    <a href="{{ route('shop.orders.show', [$o->order->id]) }}" target="_blank">{{ $o->order->name }}</a>
                                                @else
                                                    {{-- faz alguma coisa --}}
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">Nenhuma fatura a mostrar.</td>
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
        function updateDeleteModalGroup(id, route){
            $('#id-group-delete').html(id)
            $("#form-delete-group").attr('action', route);
        }

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
                "columnDefs": [{ targets: 'no-sort', orderable: false }],
                "order": [[ 1, 'desc' ]],
            });
        } );
    </script>
@endsection
