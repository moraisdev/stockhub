@extends('supplier.layout.default')

@section('title', config('app.name').' - Tutoriais')

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
                                <h3 class="mb-0">Tutoriais</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>Tutorial para fornecedores: <a href="https://youtu.be/qQFXt3Ayd24" target="_blank">assitir tutorial</a></h4>                        
                        <h4>Integração Bling: <a href="https://youtu.be/Q3hy2ejRVnE" target="_blank">assitir tutorial</a></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
