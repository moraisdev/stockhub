@extends('supplier.layout.default')

@section('title', __('supplier.register_product_tittle'))

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
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px; background-image: url(https://wallpapertag.com/wallpaper/full/5/9/b/664802-vertical-flat-design-wallpapers-1920x1080.jpg); background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
            <h1 class="display-2 text-white">{{ __('supplier.product_register') }}</h1>
                <a href="{{ route('supplier.products.index') }}" class="btn btn-secondary">{{ __('supplier.back') }}</a>
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
                            <h3 class="mb-0">{{ __('supplier.product') }}</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('supplier.products.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">{{ __('supplier.product_info') }}</h6>
                        <div class="row justify-content-center">
                            <div class="col-lg-2 col-6">
                                <div class="d-flex  justify-content-center">
                                    <div class="image-hover">
                                        <img id="product_img_source" src="{{ asset('assets/img/products/eng-product-no-image.png') }}" class="img-fluid" style="max-height:150px">
                                        <div class="middle">
                                            <button type="button" id="example_img_button" onclick="uploadProductImage()" class="btn btn-sm btn-primary">{{ __('') }}</button>
                                            <input type="file" class="d-none" id="product_img" onchange="changeProductImage(this)" name="img_source">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="category">{{ __('supplier.category') }}</label>
                                            <select id="category" class="form-control form-control-alternative" name="category">
                                                <option value="">{{ __('supplier.without_category') }}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id}}" {{ (old('category') == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_title">{{ __('supplier.tittle') }}</label>
                                            <input type="text" id="product_title" class="form-control form-control-alternative" name="title" placeholder="{{ __('supplier.tittle') }}" value="{{ old('title') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">NCM</label>
                                            <input type="text" id="product_ncm" class="form-control form-control-alternative" name="ncm" placeholder="{{ __('supplier.ncm_description') }}" value="{{ old('ncm') }}">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">EAN GTIN</label>
                                            <input type="text" id="product_ean_gtin" class="form-control form-control-alternative" name="ean_gtin" placeholder="{{ __('supplier.ean_gtin') }}" value="{{ old('ean_gtin') }}">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">{{ __('supplier.coin') }}</label>
                                            <select name="currency" id="currency" class="form-control form-control-alternative">
                                                <option value="R$" {{ (old('currency')) == 'R$' ? 'selected' : '' }}>R$</option>
                                                <option value="US$" {{ (old('currency')) == 'US$' ? 'selected' : '' }}>US$</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Isento ICMS</label>
                                            <select name="icms_exemption" id="icms_exemption" class="form-control">
                                                <option value="0" {{ old('icms_exemption') == '0' ? 'selected' : '' }}>Não</option>
                                                <option value="1" {{ old('icms_exemption') == '1' ? 'selected' : '' }}>Sim</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="products_from">{{ __('supplier.origin_products') }}</label>
                                            <select name="products_from" id="products_from" class="form-control form-control-alternative" required>
                                                <option value="BR">Brasil</option>
                                                <option value="CN">China</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_description">{{ __('supplier.description') }}</label>
                                            <textarea id="product_description" class="form-control form-control-alternative" rows="4" name="description" placeholder="{{ __('supplier.product_description') }}" required>{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <input type="checkbox" name="public" {{ (old('public') == 'on') ? 'checked' : '' }}> {{ __('supplier.public_product') }} <sup><i class="fas fa-question-circle" tooltip="true" title="{{ __('supplier.public_product_will_br') }}."></i></sup>
                                        </div>
                                    </div>
                                    @if(env('PARTICULAR') == 0)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <input type="checkbox" name="show_in_products_page" {{ (old('show_in_products_page') == 'on') ? 'checked' : '' }}> {{ __('supplier.show_my_product_page') }} <sup><i class="fas fa-question-circle" tooltip="true" title="{{ __('supplier.show_product_in_private') }}."></i></sup>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ __('supplier.options') }}</h3>
                                    </div>
                                    <a href="#!" class="btn btn-sm btn-primary" onclick="add_option()"><i class="fas fa-plus mr-1"></i> Add {{ __('supplier.options') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">{{ __('supplier.options') }}</h6>
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col-lg-12" id="options_container">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ __('supplier.variants') }}</h3>
                                    </div>
                                    <a href="#!" class="btn btn-sm btn-primary" onclick="add_variant()"><i class="fas fa-plus mr-1"></i> Add {{ __('supplier.variants') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="variants_container">

                    </div>
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ __('supplier.image_library') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <label for="files">{{ __('supplier.multiple_select_image') }}: </label>
                        <input type="file" class="form-control" id="files" name="images[]" accept="image/png, image/jpeg" multiple/>
                        <output class="row mt-4" id="result" />
                    </div>
                    @if($authenticated_user->id == 56)
                        {{-- Caso seja igual ao id da S2M2 --}}
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <h3 class="mb-0">China Division</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <label>{{ __('supplier.shipping_method_china_division') }}: </label>
                            <select name="shipping_method_china_division" id="shipping_method_china_division" class='form-control'>
                                <option value="CDEUB">CDEUB</option>
                                <option value="PUAM">PUAM</option>
                                <option value="SZEUB">SZEUB</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <label>{{ __('supplier.packing_weight_china_division') }}: </label>
                            <div class='input-group input-group-alternative flex-nowrap mb-3'>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">g</span>
                                </div>
                                <input type="number" class="form-control form-control-alternative" name="packing_weight" placeholder="{{__('supplier.weight')}}" value="10" required="">
                            </div>
                        </div>

                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <h3 class="mb-0">{{ __('supplier.automatic_discounts') }}</h3>
                                        </div>
                                        <a href="#!" class="btn btn-sm btn-primary" onclick="add_discount()"><i class="fas fa-plus mr-1"></i> Add {{ __('supplier.discount') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="discounts_container">
                            {{-- <div class='row new_discount'>
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
                            </div> --}}
                        </div>
                    @endif
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

    add_variant();

    function uploadVariantImage(variant_id){
        $('#img_'+variant_id).click()
    }

    function changeVariantImage(input) {
        if (input.files && input.files[0]) {
            var variant_id = $(input).attr('variant_id');

            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img_source_'+variant_id)
                    .attr('src', e.target.result)
                    .width('auto')
                    .height(150);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function uploadProductImage(){
        $('#product_img').click();
    }

    function changeProductImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#product_img_source')
                    .attr('src', e.target.result)
                    .width('auto')
                    .height(150);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function new_option_change(new_option){
        var id = $(new_option).attr('option_id');

        $(".new_option_"+id+"_th").text($(new_option).val());
        $(".new_option_"+id+"_td").attr('placeholder', $(new_option).val());
    }

    function add_option(){
        var option = $("#example_option").clone();
        var count = $("#new_options_count").val();

        if($(".option_class").length >= 3){
            $("#option-error").remove();
            $("#options_container").append('<div class="d-block text-danger" id="option-error">{{ __('supplier.you_cant_add_more_than_3_options') }}</div>');

            return false;
        }

        option.removeAttr('id');
        option.addClass('option_class');
        option.find('input:first').attr('name', 'new_options['+count+']');
        option.find('input:first').attr('option_id', count);
        option.find('input:first').attr('onchange', "new_option_change(this)");
        option.find('input:first').val('');
        option.find('button:first').attr('onclick', 'remove_new_option(this)');

        $("#options_container").append(option);

        append_variant_options(count);

        $("#new_options_count").val(++count);
    }

    function remove_new_option(button){
        var id = $(button).parent().parent().find('input:first').attr('option_id');

        $(".new_option_"+id+'_th').remove();
        $(".new_option_"+id+'_td').parent().parent().remove();

        $(button).parent().parent().parent().remove();

        $("#option-error").remove();
    }

    function append_variant_options(count){
        $.each($('.after_option_th'), function(key, th){
            let variant_id = $(th).attr('variant_id');

            let new_option = '<th class="new_option_'+count+'_th"></th>';

            $(new_option).insertBefore(th);
        });

        $.each($('.after_option_td'), function(key, td){
            let variant_id = $(td).attr('variant_id');

            let new_option_name = '';

            if($(td).parents('.new_variant').length >= 1) {
                new_option_name = 'new_variants['+variant_id+'][new_options]['+count+']';
            }else{
                new_option_name = 'variants['+variant_id+'][new_options]['+count+']';
            }

            let new_option = '<td>';
            new_option += ' <div class="form-group">';
            new_option += '<input type="text" class="form-control form-control-alternative new_option_'+count+'_td" name="'+new_option_name+'" required>';
            new_option += '</div>';
            new_option += '</td>';

            $(new_option).insertBefore(td);
        });
    }

    function add_variant(){
        var variant = $("#example_variant").clone();
        var count = $("#new_variants_count").val();

        variant.removeAttr('id');

        $.each(variant.find('input'), function(key, input){
            let new_name = $(input).attr('name').replace('new_variants[0]', 'new_variants['+count+']');
            $(input).attr('name', new_name);
        });

        variant.find('.after_option_td').attr('variant_id', count);
        variant.find("#example_img_button:first").removeAttr('id').attr('onclick', 'uploadVariantImage('+count+')');
        variant.find('#example_img:first').attr('id', 'img_'+count).attr('variant_id', count);
        variant.find('#example_img_source:first').attr('id', 'img_source_'+count);

        $("#variants_container").append(variant);

        $("#new_variants_count").val(++count);

        $(".decimal").maskMoney({thousands:''});
    }

    function remove_variant(button){
        $(button).parent().parent().parent().remove();
    }

    function add_discount(){
        var discount = $("#discount_example").clone();
        var countDiscount = $("#new_discounts_count").val();

        discount.removeAttr('id');

        $.each(discount.find('input'), function(key, input){
            let new_name = $(input).attr('name').replace('new_discounts[0]', 'new_discounts['+countDiscount+']');
            $(input).attr('name', new_name);
        });

        $("#discounts_container").append(discount);

        $("#new_discounts_count").val(++countDiscount);
    }

    function remove_discount(button){
        var countDiscount = $("#new_discounts_count").val();
        countDiscount--;
        $("#new_discounts_count").val(countDiscount);
        $(button).parent().parent().parent().parent().remove();
    }
</script>
@endsection
