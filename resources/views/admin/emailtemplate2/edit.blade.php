@extends('admin.layout.default')

@section('title', 'Email Edit')

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
                        <h3 class="mb-0">Email Edite</h3>
                        </div>
                        <div class="col">
                            <div class="float-right">                                
                          
                        
                        </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">      
    <div class="col-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Template Email</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        
                        <form method="POST" action="{{ route('admin.emailtemplate.update', $emails->id) }}">
                        @csrf
                        @method('PUT')
                            <div class="col-sm-12 form-group">
                                    <label></label>
                                    <textarea class="summernote" name="template" id="summernote"
                                              rows="100">{{ $emails->template }}</textarea>
                                  
                                        <div class="error text-danger"></div>
                                  
                            </div>

                            <button type="submit"
                                    class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3">
                                <span>Atualizar</span></button>
                            
                            
                        </form>
                    </div>
                    
                </div>



            </div>
        </div>
        
    </div>
    
                


















            </div>
        </div>
    </div>
   
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.css"></script>
    
<link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/lang/summernote-pt-BR.js"></script>
<script>
        $(document).ready(function() {
            $('.summernote').summernote({
                airMode: false,  
                lang: "pt-BR"
  }
  );
        });
</script>

@endsection
