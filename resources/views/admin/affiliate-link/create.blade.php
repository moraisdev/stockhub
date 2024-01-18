@extends('admin.layout.default')

@section('title', config('app.name').' - Cadastrar Link Afiliado')

@section('stylesheets')
<style type="text/css">
    .thumbnail{
        width: 100%;
        height: 250px;
        background-size: cover;
        background-position: center;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
            <h1 class="display-2 text-white">Cadastrar Link Afiliado</h1>
                <a href="{{ route('admin.affiliate-link.index') }}" class="btn btn-secondary">{{ __('supplier.back') }}</a>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-2">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Link Afiliado</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.affiliate-link.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">Informações do Link Afiliado</h6>
                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_title">Nome Afiliado</label>
                                            <input type="text" id="product_title" class="form-control form-control-alternative" name="name" placeholder="Nome do Afiliado" value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Tipo de Link</label>
                                            <select class="form-control" id="type" name='type'>
                                              <option value='shop'>Lojista</option>
                                              <option value='supplier'>Fornecedor</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer pb-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="float-right form-group">
                                    <a href="{{ route('supplier.products.index') }}" class="btn btn-secondary">{{ __('supplier.cancel') }}</a>
                                    <button class="btn btn-primary">{{ __('supplier.register') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>

<script type="text/javascript">
    //CKEDITOR.replace('product_description');

    window.onload = function(){
        //Check File API support
        if(window.File && window.FileList && window.FileReader)
        {
            var filesInput = document.getElementById("files");
            filesInput.addEventListener("change", function(event){
                var files = event.target.files; //FileList object
                var output = document.getElementById("result");

                output.innerHTML = '';

                for(var i = 0; i< files.length; i++)
                {
                    var file = files[i];
                    //Only pics
                    if(!file.type.match('image'))
                        continue;
                    var picReader = new FileReader();
                    picReader.addEventListener("load",function(event){
                        var picFile = event.target;
                        var div = document.createElement("div");
                        div.setAttribute('class', 'd-flex col-lg-3 col-md-6 col-12 align-items-center justify-content-center');

                        var thumb = document.createElement("div");
                        thumb.setAttribute('class', 'thumbnail');
                        thumb.style.backgroundImage = "url('"+ picFile.result.replace(/(\r\n|\n|\r)/gm, "") + "')";

                        div.innerHTML = thumb.outerHTML;

                        output.insertBefore(div,null);
                    });
                    //Read the image
                    picReader.readAsDataURL(file);
                }
            });
        }
        else
        {
            console.log("Browser not supported");
        }
    }

    function uploadImage(){
        $('#img_source').click();
    }

    function uploadImageMobile(){
        $('#img_source_mobile').click();
    }

    function changeImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img_source_preview')
                    .attr('src', e.target.result)
                    .width('auto')
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function changeImageMobile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img_source_mobile_preview')
                    .attr('src', e.target.result)
                    .width('auto')
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
