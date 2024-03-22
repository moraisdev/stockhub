@extends('supplier.layout.default')

@section('stylesheets')

<style type="text/css">
        .btn-circle {
            padding: 5px 8px;
            border-radius: 50%;
            font-size: 0.8rem;
            width: 2.0rem !important;
            height: 2.0rem !important;
        }

        .current {
            background-color: #E4001B !important;
            }
    </style>
@endsection

@section('content')       
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
            </div>
        </div>
    </div>
    <div class="container-fluid mt--7" >
        <div class="row">
            <div class="col-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Radar</h3>
                            </div>
                            <div class="col-10">
                                <div class="float-right">
                                    <a class="btn btn-primary" href=""><i
                                            class="fas fa-plus mr-2"></i> Criar Pedido </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body my-0">
                        <p class="my-0"> Listagem de todos as solicitações de radares
                        </p>
                    </div>
                    <div class="col-12 table-responsive">
                            <table class="table align-items-center table-flush data-table mdl-data-table dataTable">
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" class="text-center">Cliente</th>
                                    <th scope="col">Criação</th>
                                    <th scope="col">Atualiazação</th>
                                    <th scope="col" class="text-center">Status</th>
                                    <th scope="col" class="actions-th">Ações</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
                            <h5 class="modal-title">{{ __('supplier.product_delete') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>{{ __('supplier.confirm_product_delete') }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('supplier.cancel') }}</button>
                            <button class="btn btn-danger">{{ __('supplier.delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

        <!-- Modal para visualização do PDF -->
        <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">Visualização do PDF</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="pdfIframe" src="" style="width:100%; height:500px;" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
            </div>
        </div>
        </div>

@endsection
@section('scripts')

<script>
$(document).ready(function() {
    $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplier.radar.tabelas') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {
                data: 'responsible_name', name: 'responsible_name', orderable: true, searchable: true,
                render: function(data, type, row) {
                    return data;
                }
            },

            {data: 'created_at', name: 'created_at', orderable: true, searchable: true},
            {data: 'updated_at', name: 'updated_at', orderable: true, searchable: true},
            {data: 'status', name: 'status', orderable: true, searchable: true, 
                render: function(data, type, row) {
                    switch(data) {
                        case 'EM ANALISE':
                            return '<span class="badge badge-warning">Em análise</span>';
                        case 'PAGO':
                            return '<span class="badge badge-warning">Pago</span>';
                        case 'REJEITADO':
                            return '<span class="badge badge-warning">Rejeitado</span>';
                        case 'CANCELADO':
                            return '<span class="badge badge-warning">Cancelado</span>';
                        case 'ATIVO':
                            return '<span class="badge badge-dark">Ativo</span>';
                        default:
                            return '<span class="badge badge-secondary">Desconhecido</span>';
                    }
                }
            },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        language: {
            lengthMenu: "Mostrando _MENU_ registros por página",
            zeroRecords: "Nada encontrado",
            info: "Mostrando página _PAGE_ de _PAGES_",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
            search: "Buscar Cliente:",
            paginate: {
                previous: "<",
                next: ">"
            },
        }
    });
});

function edit(id)
{
    let url = "{{ route('supplier.radar.edit', ':id') }}";
    url = url.replace(':id', id);
    document.location.href=url;
   
}
</script>
@endsection
