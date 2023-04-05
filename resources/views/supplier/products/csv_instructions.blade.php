@extends('supplier.layout.default')

@section('title', __('supplier.products'))

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
                                <h3 class="mb-0">{{ trans('supplier.importar_produtos_execel') }}</h3>
                            </div>
                            <div class="col">
                                <div class="float-right">
                                    <a class="btn btn-secondary" href="{{ route('supplier.products.index') }}"><i class="fas fa-arrow-left mr-2"></i> {{ trans('supplier.back') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 my-0">
                        <p class="my-0">
                        {{ trans('supplier.text_baixar_tabela_produtos_excel_01') }} <span class="text-success">{{ trans('supplier.excel') }}</span> {{ trans('supplier.text_e_o') }} <span class="text-success">{{ trans('supplier.libreoffice') }}</span>.
                        </p>
                        <p class="my-1">
                        {{ trans('supplier.texrt_baixar_tabela_produtos_excel') }}
                        </p>
                        <div class="text-center">
                            <a href="{{asset('assets/static/PlanilhaModeloProduto.xlsx')}}" class="btn btn-primary mt-2">{{ trans('supplier.clique_para_baixar_modelo') }}</a>
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
                                <h3 class="mb-0">{{ trans('supplier.importar_xlsx_excel') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 my-0">
                        <p class="my-0">
                        {{ trans('supplier.text_faca_upload_arquivo_excel') }} {{config('app.name')}}.
                        </p>
                        <div class="row mt-4">
                            <div class="col-lg-6 offset-lg-3">
                            <form action="{{ route('supplier.products.import.csv') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                                <div class="form-group">
                                    <label for="arquivo" class="control-label">{{ trans('supplier.arquivo_excel') }}</label>
                                    <input type="file" class="form-control" name="arquivo" id="arquivo">
                                </div>
                                <button  type="submit" class="btn btn-primary btn-block mt-2">{{ trans('supplier.importar_arquivo') }}</button>
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
