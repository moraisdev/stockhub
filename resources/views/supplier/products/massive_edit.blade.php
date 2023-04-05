@extends('supplier.layout.default')

@section('title', __('supplier.edit_product_tittle'))

@section('content')
    <div class="header pb-2 pt-2 pt-lg-4 d-flex align-items-center" style="min-height: 400px;">
        <!-- Mask -->
        <span class="mask bg-gradient-default opacity-8"></span>
        <!-- Header container -->
        <div class="container-fluid d-flex align-items-center">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <a href="{{ route('supplier.products.index') }}"
                        class="btn btn-secondary">{{ __('supplier.back') }}</a>
                    <a class="btn btn-warning" href="#" id="massiveEdit"
                        onclick="document.getElementById('formUpdate').submit()"><i class="fas fa-edit mr-2"></i>
                        {{ trans('supplier.atualizar_produtos') }} </a>
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
                    <div class="card-body my-0">
                        <p class="my-0">{{ trans('supplier.listagem_produtos_selecionados_massa') }}</p>
                    </div>
                    <div class="table-responsive">
                        <!-- Projects table -->
                        <table class="table align-items-center table-flush">
                            <form action="{{ url('supplier/products/massive-update') }}" method="post" id="formUpdate">
                                @csrf
                                @isset($products)
                                    @forelse($products as $product)
                                        <thead class="thead-light">
                                            <tr class="bg-light ">
                                                <td colspan="9"><strong>{{ $product->title }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="col">{{ trans('supplier.category') }}</th>
                                                <th scope="col">{{ trans('supplier.titulo') }}</th>
                                                <th scope="col">{{ trans('supplier.ncm') }}</th>
                                                <th scope="col">{{ trans('supplier.origem') }}</th>
                                                <th scope="col">{{ trans('supplier.coin') }}</th>
                                                <th scope="col">{{ trans('supplier.icms') }}</th>
                                                <th scope="col">{{ trans('supplier.description') }}</th>
                                                <th scope="col">{{ trans('supplier.public') }}</th>
                                                <th scope="col">{{ trans('supplier.pergunta_mostrar_pagina') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <input class="form-control form-control-alternative" hidden type="text"
                                                        name="id[]" id="" value={{ $product->id }} required>

                                                    <select id="category" class="form-control form-control-alternative"
                                                        name="category[]" required>
                                                        <option value="">{{ __('supplier.without_category') }}</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('category', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    {{-- <input class="form-control form-control-alternative" type="text" name="category[]" id=""
                                    value={{ $product->category ? $product->category->name : 'N/A' }}> --}}
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-alternative"
                                                        name="title[]" placeholder="{{ __('supplier.product_tittle') }}"
                                                        value="{{ $product->title }}" required>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-alternative" type="text"
                                                        name=ncm[]"" id="" value={{ $product->ncm }} required>
                                                </td>
                                                <td>
                                                    <select name="products_from[]" id="products_from"
                                                        class="form-control form-control-alternative" required>
                                                        <option value="BR"
                                                            {{ $product->products_from == 'BR' ? 'selected' : '' }}>Brasil
                                                        </option>
                                                        <option value="CN"
                                                            {{ $product->products_from == 'CN' ? 'selected' : '' }}>China
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="currency[]" id="currency" class="form-control" required>
                                                        <option value="R$"
                                                            {{ $product->currency == 'R$' ? 'selected' : '' }}>R$</option>
                                                        <option value="US$"
                                                            {{ $product->currency == 'US$' ? 'selected' : '' }}>US$</option>
                                                </td>
                                                <td>
                                                    <select name="icms_exemption[]" id="icms_exemption" class="form-control"
                                                        required>
                                                        <option value="0"
                                                            {{ $product->icms_exemption == '0' ? 'selected' : '' }}>{{ trans('supplier.nao') }}
                                                        </option>
                                                        <option value="1"
                                                            {{ $product->icms_exemption == '1' ? 'selected' : '' }}>{{ trans('supplier.sim') }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-alternative" type="text"
                                                        name="description[]" id="" value={{ $product->description }}
                                                        required>
                                                </td>
                                                <td>
                                                    <select name="public[]" id="public" class="form-control" required>
                                                        <option value="0" {{ $product->public == '0' ? 'selected' : '' }}>{{ trans('supplier.nao') }}
                                                        </option>
                                                        <option value="1" {{ $product->public == '1' ? 'selected' : '' }}>{{ trans('supplier.sim') }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="show_in_products_page[]" id="show_in_products_page"
                                                        class="form-control" required>
                                                        <option value="0"
                                                            {{ $product->show_in_products_page == '0' ? 'selected' : '' }}>
                                                            {{ trans('supplier.nao') }}
                                                        </option>
                                                        <option value="1"
                                                            {{ $product->show_in_products_page == '1' ? 'selected' : '' }}>
                                                            {{ trans('supplier.sim') }}
                                                        </option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <thead>
                                                <th>{{ trans('supplier.sku') }}</th>
                                                <th>{{ __('supplier.stock') }}</th>
                                                @foreach ($product->options as $option)
                                                    <th class="option_{{ $option->id }}_th">{{ $option->name }}</th>
                                                @endforeach
                                                <th class="after_option_th">{{ trans('supplier.money_price') }}</th>
                                                @if ($authenticated_user->id == 56)
                                                    <th>{{ __('supplier.factory_price') }}</th>
                                                @endif
                                                {{-- <th>Custo</th> --}}
                                                <th>{{ __('supplier.weight') }}</th>
                                                <th>{{ __('supplier.width') }}</th>
                                                <th>{{ __('supplier.height') }}</th>
                                                <th>{{ __('supplier.depth') }}</th>
                                            </thead>
                                            @forelse($product->variants as $variant)
                                        <tbody>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control form-control-alternative"
                                                        name="variants[{{ $variant->id }}][sku]"
                                                        value="{{ $variant->sku }}" placeholder="{{ trans('supplier.sku') }}" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="number" class="form-control form-control-alternative"
                                                        name="variants[{{ $variant->id }}][stock]"
                                                        value="{{ $variant->stock ? $variant->stock->quantity : 0 }}"
                                                        placeholder="{{ __('supplier.stock_quantity') }}" required>
                                                </div>
                                            </td>

                                            @foreach ($variant->options_values as $option_value)
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text"
                                                            class="form-control form-control-alternative option_{{ $option_value->product_option_id }}_td"
                                                            name="variants[{{ $variant->id }}][options][{{ $option_value->product_option_id }}]"
                                                            placeholder="{{ $option_value->option ? $option_value->option->name : '' }}"
                                                            value="{{ $option_value->value }}" required>
                                                    </div>
                                                </td>
                                            @endforeach

                                            <td class="after_option_td" variant_id="{{ $variant->id }}">
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">{{ $product->currency }}</span>
                                                        </div>
                                                        <input type="text" class="form-control form-control-alternative decimal"
                                                            name="variants[{{ $variant->id }}][price]"
                                                            placeholder="{{ __('supplier.price') }}"
                                                            value="{{ $variant->price }}" required>
                                                    </div>
                                                </div>
                                            </td>
                                            @if ($authenticated_user->id == 56)
                                                <td>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                            <div class="input-group-prepend">
                                                                <span
                                                                    class="input-group-text">{{ $product->currency }}</span>
                                                            </div>
                                                            <input type="text"
                                                                class="form-control form-control-alternative decimal"
                                                                name="variants[{{ $variant->id }}][internal_cost]"
                                                                placeholder="{{ __('supplier.factory_price') }}"
                                                                value="{{ $variant->internal_cost }}">
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                            {{-- <td>
                                    <div class="form-group">
                                        <div class="input-group input-group-alternative flex-nowrap mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">R$</span>
                                            </div>
                                            <input type="text" class="form-control form-control-alternative decimal" name="variants[{{ $variant->id }}][cost]" placeholder="Custo" value="{{ $variant->cost }}" required>
                                        </div>
                                    </div>
                                </td> --}}
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">g</span>
                                                        </div>
                                                        <input type="number" class="form-control form-control-alternative"
                                                            name="variants[{{ $variant->id }}][weight_in_grams]"
                                                            placeholder="{{ __('supplier.weigth') }}"
                                                            value="{{ $variant->weight_in_grams }}" required>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <input type="text" class="form-control form-control-alternative decimal"
                                                            name="variants[{{ $variant->id }}][width]"
                                                            placeholder="{{ __('supplier_width') }}"
                                                            value="{{ $variant->width }}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <input type="text" class="form-control form-control-alternative decimal"
                                                            name="variants[{{ $variant->id }}][height]"
                                                            placeholder="{{ __('supplier.height') }}"
                                                            value="{{ $variant->height }}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <input type="text" class="form-control form-control-alternative decimal"
                                                            name="variants[{{ $variant->id }}][depth]"
                                                            placeholder="{{ __('supplier.depth') }}"
                                                            value="{{ $variant->depth }}">
                                                    </div>
                                                </div>
                                            </td>
                                        </tbody>
                                    @empty
                                        <p>{{ __('supplier.no_variant_register') }}.</p>
                                    @endforelse
                                @empty
                                    <tr>
                                        <th scope="row" colspan="6">
                                        {{ trans('supplier.nenhum_produto_selecionado') }}
                                        </th>
                                    </tr>
                                    @endforelse
                                @endisset
                            </form>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="card-footer py-4"> --}}
    {{-- <div class="float-right">
            {{ $products->render() }}
        </div> --}}
    {{-- </div> --}}
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>

    <script type="text/javascript">
        //CKEDITOR.replace('product_description');

        window.onload = function() {
            //Check File API support
            if (window.File && window.FileList && window.FileReader) {
                var filesInput = document.getElementById("files");
                filesInput.addEventListener("change", function(event) {
                    var files = event.target.files; //FileList object
                    var output = document.getElementById("result");

                    output.innerHTML = '';

                    for (var i = 0; i < files.length; i++) {
                        var file = files[i];
                        //Only pics
                        if (!file.type.match('image'))
                            continue;
                        var picReader = new FileReader();
                        picReader.addEventListener("load", function(event) {
                            var picFile = event.target;
                            var div = document.createElement("div");
                            div.setAttribute('class',
                                'd-flex col-lg-3 col-md-6 col-12 align-items-center justify-content-center'
                            );

                            var thumb = document.createElement("div");
                            thumb.setAttribute('class', 'thumbnail');
                            thumb.style.backgroundImage = "url('" + picFile.result.replace(
                                /(\r\n|\n|\r)/gm, "") + "')";

                            div.innerHTML = thumb.outerHTML;

                            output.insertBefore(div, null);
                        });
                        //Read the image
                        picReader.readAsDataURL(file);
                    }
                });
            } else {
                console.log("Browser not supported");
            }
        }

    </script>
@endsection
