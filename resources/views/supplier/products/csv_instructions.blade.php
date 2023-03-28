@extends('supplier.layout.default')

@section('title', 'Produtos')

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
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8"></div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Importação de produtos através de um arquivo do Excel</h3>
                            </div>
                            <div class="col">
                                <div class="float-right">
                                    <a class="btn btn-secondary" href="{{ route('supplier.products.index') }}"><i class="fas fa-arrow-left mr-2"></i> Voltar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 my-0">
                        <p class="my-0">
                            Sabemos que o processo de importação manual de seus produtos para um novo sistema pode ser muito cansativa, por isso fornecemos uma ferramenta de importação de seus produtos através de um arquivo Excel, que pode ser gerado pela maioria dos aplicativos de gerenciamento de tabelas, como o <span class="text-success">Excel</span> e o <span class="text-success">LibreOffice Calc</span>.
                        </p>
                        <p class="my-1">
                            Basta você baixar a nossa tabela modelo de importação de produtos e abrir o arquivo com seu gerenciador de tabelas. Após preencher a tabela com todos os seus produtos não se esqueça de salvar o arquivo antes de realizar o upload!
                        </p>
                        <div class="text-center">
                            <a href="{{asset('assets/static/PlanilhaModeloProduto.xlsx')}}" class="btn btn-primary mt-2">Clique aqui para baixar o arquivo modelo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Importar arquivo XLSX (Excel)</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 my-0">
                        <p class="my-0">
                            Faça o upload do arquivo excel com seus produtos através do formulário abaixo para importá-los para o {{config('app.name')}}.
                        </p>
                        <div class="row mt-4">
                            <div class="col-lg-6 offset-lg-3">
                            <form action="{{ route('supplier.products.import.csv') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                                <div class="form-group">
                                    <label for="arquivo" class="control-label">Arquivo Excel</label>
                                    <input type="file" class="form-control" name="arquivo" id="arquivo">
                                </div>
                                <button  type="submit" class="btn btn-primary btn-block mt-2">Importar arquivo</button>
                            </div>
                            </form>
                        </div>
                    </div>
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
