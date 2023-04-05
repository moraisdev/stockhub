@extends('supplier.layout.default')

@section('title', __('supplier.tutoriais_title'))

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
            <div class="col-6 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{ trans('supplier.tutoriais_title') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>{{ trans('supplier.tutorial_fornecedores') }} <a href="https://youtu.be/qQFXt3Ayd24" target="_blank">{{ trans('supplier.assistir_tutorial') }}</a></h4>                        
                        <h4>{{ trans('supplier.integracao_bling') }} <a href="https://youtu.be/Q3hy2ejRVnE" target="_blank">{{ trans('supplier.assistir_tutorial') }}</a></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
