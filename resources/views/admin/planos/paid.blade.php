@extends('admin.layout.default')

@section('title', 'Assinaturas Liquidadas ')

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
                            <h3 class="mb-0">Sem Movimento</h3>
                        </div>
                        <div class="col">
                            <div class="float-right">
                              
                            </div>
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