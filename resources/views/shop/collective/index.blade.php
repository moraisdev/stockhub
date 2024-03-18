@extends('shop.layout.default')
<style>
.status-dot {
    height: 10px;
    width: 10px;
    background-color: #ee3a1f;
    border-radius: 50%;
    display: inline-block;
    margin-left:5px;
}

.status-text {
    color: #ee3a1f;
}

</style>
@section('content')
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-default"></span>
        <div class="container-fluid">
    </div>
</div>
    <div class="container-fluid mt--7" >
        <div class="row">
            <div class="col-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <h3 class="mb-0">Importação Coletiva</h3>
                            </div>
                            <div class="col">
                                <div class="float-right">
                                    <a class="btn btn-primary" href="{{ route('shop.collective.new') }}"><i
                                            class="fas fa-plus mr-2"></i> Criar Pedido </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 table-responsive">
                            <table class="table align-items-center table-flush data-table mdl-data-table dataTable">
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Criação</th>
                                    <th scope="col">Atualiazação</th>
                                    <th scope="col">Custo da Importação</th>
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

@endsection

@section('scripts')

<script>
$(document).ready(function() {
    $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.collective.tabelas') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'type_order', name: 'type_order', orderable: true, searchable: true,
                render: function(data, type, row) {
                    return data == 1 ? 'Pessoa Física' : data == 2 ? 'Pessoa Jurídica' : 'Desconhecido';
                }
            },

            {data: 'created_at', name: 'created_at', orderable: true, searchable: true},
            {data: 'updated_at', name: 'updated_at', orderable: true, searchable: true},
            {data: 'cost_price', name: 'cost_price', orderable: true, searchable: true,
                render: function(data, type, row) {
                    if(data === null) {
                        return '-';
                    } else {
                        var percentualTaxa = 0.0599;
                        var taxaFixa = 1.00;
                        var valorComTaxas = (parseFloat(data) * (1 + percentualTaxa)) + taxaFixa;
                        return 'R$ ' + valorComTaxas.toFixed(2);
                    }
                }
            },

            {data: 'status', name: 'status', orderable: true, searchable: true, 
                render: function(data, type, row) {
                    switch(data) {
                        case 'EM ANALISE':
                            return '<span class="badge badge-warning">Em análise</span>';
                        case 'REJEITADO':
                            return '<span class="badge badge-warning">Rejeitado</span>';
                        case 'CANCELADO':
                            return '<span class="badge badge-warning">Cancelado</span>';
                        case 'PAGAMENTO PENDENTE':
                            return '<span class="badge badge-info">Pagamento Pendente</span>';
                        case 'PAGO':
                            return '<span class="badge badge-success">Pago</span>';
                        case 'RECEBIDO NO ARMAZEM CHINA':
                            return '<span class="badge badge-success">Recebido no Armazém China</span>';
                        case 'EM PROCESSO DE EMBARQUE':
                            return '<span class="badge badge-success">Em Processo de Embarque</span>';
                        case 'EM ROTA MARITIMA':
                            return '<span class="badge badge-success">Em Rota Marítima</span>';
                        case 'DESPACHO E LIBERAÇÃO NO PORTO BRASIL':
                            return '<span class="badge badge-success">⁠Em Processo de Despacho e Liberação no Porto Brasil</span>';
                        case 'ENVIADO':
                            return '<span class="badge badge-primary">Enviado</span>';
                        case 'ENTREGUE':
                            return '<span class="badge badge-dark">Entregue</span>';
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

function show(id) {    
    let url = "{{ route('shop.collective.show', ':id') }}";
    url = url.replace(':id', id);
    document.location.href=url;
}

function buy_stripe(id) {
    let url = "{{ route('shop.collective.buy', ':id') }}";
    url = url.replace(':id', id);
    document.location.href=url;
}

</script>
@endsection
