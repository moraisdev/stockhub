@extends('admin.layout.default')

@section('title', config('app.name').' - Visualizar Banner')

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
            <h1 class="display-2 text-white">Visualizar Banner</h1>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">{{ __('supplier.back') }}</a>
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
                            <h3 class="mb-0">Banner</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">Informações do Banner</h6>
                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12 col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_title">Nome</label>
                                            <input type="text" id="product_title" class="form-control form-control-alternative" name="name" placeholder="Nome do Banner" value="{{ $banner->name }}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_title">Link</label>
                                            <input type="text" id="product_title" class="form-control form-control-alternative" name="link" placeholder="Link do Banner" value="{{ $banner->link ? $banner->link : '' }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex  justify-content-center">
                                    <div class="image-hover">
                                        <img id="img_source_preview" src="{{ $banner->img_source }}" class="img-fluid">
                                    </div>
                                </div>
                                <h5 class="text-center" for="img_source">Imagem Computador 1593x228 pixels</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex  justify-content-center text-center" >                                    
                                    <div class="image-hover">
                                        <img id="img_source_preview" src="{{ $banner->img_source_mobile ? $banner->img_source_mobile : '' }}" class="img-fluid" >
                                    </div>
                                </div>
                                <h5 class="text-center" for="img_source_mobile">Imagem Mobile/Celular 800x800 pixels</h5>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="d-none">
    <input type="hidden" id="new_variants_count" value="0">
    <input type="hidden" id="new_options_count" value="0">
    <input type="hidden" id="new_discounts_count" value="0">

    <div class='row new_discount' id='discount_example'>
        <div class='col-lg-6'>
            <div class='table-responsive'>
                <table class='table table-borderless variant-fields-table'>
                    <thead>
                        <tr>
                            <th>Quantidade</th>
                            <th>Desconto(%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class='form-group'>
                                    <input type="number" class='form-control form-control-alternative' name='new_discounts[0][quantity]' placeholder="Quantidade">
                                </div>
                            </td>
                            <td>
                                <div class='form-group'>
                                    <input type="number" step="0.01" class='form-control form-control-alternative' name='new_discounts[0][value]' placeholder="Desconto">
                                </div>
                            </td>
                            <td>
                                <button class='btn btn-danger btn-sm' type='button' onclick="remove_discount(this)">{{__('supplier.remove_discount')}}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-group d-inline-block mr-1" style="max-width: 150px" id="example_option">
        <div class="input-group input-group-alternative flex-nowrap mb-3">
            <input type="text" class="form-control form-control-alternative" placeholder="{{ __('supplier.option_name') }}" name="options[]" value="" required>
            <div class="input-group-append">
                <button class="btn btn-sm btn-danger" type="button" onclick="remove_option(this)"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>

    <div class="row justify-content-center align-items-center new_variant" id="example_variant">
        <div class="col-lg-2 col-6">
            <div class="d-flex justify-content-center">
                <div class="image-hover">
                    <img id="example_img_source" src="{{ asset('assets/img/products/eng-product-no-image.png') }}" class="img-fluid" style="max-height:150px">
                    <div class="middle">
                        <button type="button" id="example_img_button" onclick="uploadVariantImage(0)" class="btn btn-sm btn-primary">{{ __('supplier.change_image') }}</button>
                        <input type="file" class="d-none" id="example_img" onchange="changeVariantImage(this)" variant_id="0" name="new_variants[0][img_source]">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="float-right">
                <button class="btn btn-danger btn-sm" type="button" onclick="remove_variant(this)">{{ __('supplier.remove_variant') }}</button>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless variant-fields-table">
                    <thead>
                        <th>SKU</th>
                        <th>{{ __('supplier.stock') }}</th>
                        <th class="after_option_th">{{ __('supplier.price') }}</th>
                        @if($authenticated_user->id == 56)
                            <th>{{ __('supplier.factory_price') }}</th>
                        @endif
                        {{--<th>Custo</th>--}}
                        <th>{{ __('supplier.weight') }}</th>
                        <th>{{ __('supplier.width') }}</th>
                        <th>{{ __('supplier.height') }}</th>
                        <th>{{ __('supplier.depth') }}</th>
                    </thead>
                    <tbody>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-alternative" name="new_variants[0][sku]" placeholder="SKU" required>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" class="form-control form-control-alternative" name="new_variants[0][stock]" placeholder="{{ __('supplier.stock_quantity') }}" required>
                            </div>
                        </td>
                        <td class="after_option_td" variant_id="0">
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][price]" placeholder="{{ __('supplier.money_price') }}" required>
                                </div>
                            </div>
                        </td>
                        @if($authenticated_user->id == 56)
                            <td>
                                <div class="form-group">
                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][internal_cost]" placeholder="{{ __('supplier.factory_price') }}">
                                    </div>
                                </div>
                            </td>
                        @endif
                        {{--<td>
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">R$</span>
                                    </div>
                                    <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][cost]" placeholder="Custo" required>
                                </div>
                            </div>
                        </td>--}}
                        <td>
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">g</span>
                                    </div>
                                    <input type="number" class="form-control form-control-alternative" name="new_variants[0][weight_in_grams]" placeholder="{{ __('supplier.weight') }}" required>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][width]" placeholder="{{ __('supplier.width') }}">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][height]" placeholder="{{ __('supplier.height') }}">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][depth]" placeholder="{{ __('supplier.depth') }}">
                                </div>
                            </div>
                        </td>
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="my-4 w-100" />
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
                    .height(150);
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
                    .height(150);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
