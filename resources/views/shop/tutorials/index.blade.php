@extends('shop.layout.default')

@section('content')
    <!-- Header -->
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <span class="mask bg-gradient-default"></span> 
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
                    @foreach($tutorial as $tuto)
                        <h4>{{$tuto->descricao}} : <a href="{{$tuto->link}}" target="_blank">Assitir tutorial</a></h4>                        
                     
                       @endforeach 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
