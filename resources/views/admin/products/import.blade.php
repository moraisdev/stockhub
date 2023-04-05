<html>
    <head>
        <title>{{ trans('supplier.cadastro_rapido_produtos') }}</title>
        <link href="{{ asset('assets/css/argon-dashboard.css?v=1.1.1') }}" rel="stylesheet" />
        <script src="https://kit.fontawesome.com/7b818e6d8e.js" crossorigin="anonymous"></script>

        <style>
            .table tbody tr td{
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <form method="POST" action="{{ route('admin.products.import.post', $supplier_id) }}">
            @csrf
            <div class="row mx-1 my-2">
                <div class="col">
                    <h3 class="d-inline">{{ trans('supplier.cadastro_rapido_produtos') }}</h3>
                    <div class="d-inline flex-grow-1">
                        <div class="float-right">
                            <a href="{{ asset('assets/img/import-example.png') }}" target="_blank" class="btn btn-warning btn-sm" {{--data-toggle="modal" data-target="#modal-example"--}}><i class="fas fa-info"></i>{{ trans('supplier.ver_modelo_exemplo') }}</a>
                            <a href="{{ route('admin.products.update_descriptions', $supplier_id) }}" target="_blank" class="btn btn-primary btn-sm" {{--data-toggle="modal" data-target="#modal-example"--}}><i class="fas fa-box"></i>{{ trans('supplier.atualizar_descricoes') }}</a>
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-import-csv">
                                <i class="fa fa-file-excel-o"></i> {{ trans('supplier.importar_csv') }}
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="addNewProduct()"><i class="fas fa-plus"></i>{{ trans('supplier.adicionar_produtos') }}</button>
                            <button class="btn btn-primary btn-sm">{{ trans('supplier.concluir_cadastro') }}</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-default btn-sm">{{ trans('supplier.voltar_sistema') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered">
                <thead class="bg-white">
                    <tr>
                        <th></th>
                        <th>{{ trans('supplier.tipo') }}</th>
                        <th>{{ trans('supplier.identificador') }}</th>
                        <th>{{ trans('supplier.category') }}</th>
                        <th>{{ trans('supplier.tittle') }}</th>
                        <th>{{ trans('supplier.ncm') }}</th>
                        <th>{{ trans('supplier.description') }}</th>
                        <th>{{ trans('supplier.public') }}?</th>
                        <th>{{ trans('supplier.options') }}</th>
                        <th>{{ trans('supplier.valores_opcoes') }}</th>
                        <th>{{ trans('supplier.money_price') }}</th>
                        <th>{{ trans('supplier.sku') }}</th>
                        <th>{{ trans('supplier.weight') }}</th>
                        <th>{{ trans('supplier.width') }}</th>
                        <th>{{ trans('supplier.height') }}</th>
                        <th>{{ trans('supplier.depth') }}</th>
                        {{--<th>{{ trans('supplier.foto') }}</th>--}}
                    </tr>
                </thead>
                <tbody id="products-table">
                    @if(isset($csv_products) && count($csv_products) > 0)
                        @foreach($csv_products as $count => $line)
                            <tr count="{{ $count }}" class="checkType">
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeLine(this)"><i class="fas fa-times"></i></button>
                                </td>
                                <td style="min-width: 130px">
                                    <select name="products[{{ $count }}][type]" class="form-control form-control-sm" onchange="changeType(this)">
                                        <option value="product" {{ strtolower($line['type']) == 'produto' ? 'selected' : '' }}>{{ trans('supplier.product') }}</option>
                                        <option value="variant" {{ strtolower($line['type']) == 'variante' ? 'selected' : '' }}>{{ trans('supplier.variante') }}</option>
                                    </select>
                                </td>
                                <td style="min-width: 200px" id="identifier_td">
                                    <input type="text" class="form-control form-control-sm" name="products[{{ $count }}][identifier]" placeholder="{{ trans('supplier.identificador') }}" onchange="updateIdentifier(this)" value="{{ strtolower($line['identifier']) }}">
                                </td>
                                <td style="min-width: 130px">
                                    <select name="products[{{ $count }}][category_id]" class="form-control form-control-sm">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ strtolower($line['category']) == strtolower($category->name) ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][title]" placeholder="{{ trans('supplier.tittle') }}" value="{{ $line['title'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][ncm]" placeholder="{{ trans('supplier.ncm') }}" value="{{ $line['ncm'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][description]" placeholder="{{ trans('supplier.description') }}" value="{{ $line['description'] }}"></td>
                                <td style="min-width: 110px">
                                    <select name="products[{{ $count }}][public]" class="form-control form-control-sm">
                                        <option value="0" {{ (strtolower($line['public']) == 'nao' || strtolower($line['public']) == 'não') ? 'selected' : '' }}>{{ trans('supplier.nao') }}</option>
                                        <option value="1" {{ (strtolower($line['public']) == 'sim') ? 'selected' : '' }}>{{ trans('supplier.sim') }}</option>
                                    </select>
                                </td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][options]" placeholder="{{ trans('supplier.options') }}" value="{{ $line['options'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ $count }}][options_values]" placeholder="{{ trans('supplier.valores_opcoes') }}" value="{{ $line['options_values'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 125px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ $count }}][price]" placeholder="{{ trans('supplier.money_price') }}" value="{{ $line['price'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 150px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ $count }}][sku]" placeholder="{{ trans('supplier.sku') }}" value="{{ $line['sku'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 100px"><input type="number" class="form-control form-control-sm variant_only_input" name="products[{{ $count }}][weight_in_grams]" placeholder="Peso" value="{{ $line['weight_in_grams'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ $count }}][width]" placeholder="Largura" value="{{ $line['width'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ $count }}][height]" placeholder="Altura" value="{{ $line['height'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ $count }}][depth]" placeholder="Profundidade" value="{{ $line['depth'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                            </tr>
                            @if(strtolower($line['type']) == 'produto' && $line['sku'] != '')
                                <tr count="{{ ($count+10000) }}" class="checkType">
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeLine(this)"><i class="fas fa-times"></i></button>
                                    </td>
                                    <td style="min-width: 130px">
                                        <select name="products[{{ ($count+10000) }}][type]" class="form-control form-control-sm" onchange="changeType(this)">
                                            <option value="product">{{ trans('supplier.product') }}</option>
                                            <option value="variant" selected>{{ trans('supplier.variante') }}</option>
                                        </select>
                                    </td>
                                    <td style="min-width: 200px" id="identifier_td">
                                        <input type="text" class="form-control form-control-sm" name="products[{{ ($count+10000) }}][identifier]" placeholder="{{ trans('supplier.identificador') }}" onchange="updateIdentifier(this)" value="{{ strtolower($line['identifier']) }}">
                                    </td>
                                    <td style="min-width: 130px">
                                        <select name="products[{{ ($count+10000) }}][category_id]" class="form-control form-control-sm">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ strtolower($line['category']) == strtolower($category->name) ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ ($count+10000) }}][title]" placeholder="{{ trans('supplier.tittle') }}" value="{{ $line['title'] }}"></td>
                                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ ($count+10000) }}][ncm]" placeholder="{{ trans('supplier.ncm') }}" value="{{ $line['ncm'] }}"></td>
                                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ ($count+10000) }}][description]" placeholder="{{ trans('supplier.description') }}" value="{{ $line['description'] }}"></td>
                                    <td style="min-width: 110px">
                                        <select name="products[{{ ($count+10000) }}][public]" class="form-control form-control-sm">
                                            <option value="1" {{ (strtolower($line['public']) == 'sim') ? 'selected' : '' }}>{{ trans('supplier.sim') }}</option>
                                            <option value="0" {{ (strtolower($line['public']) == 'nao' || strtolower($line['public']) == 'não') ? 'selected' : '' }}>{{ trans('supplier.nao') }}</option>
                                        </select>
                                    </td>
                                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ ($count+10000) }}][options]" placeholder="{{ trans('supplier.options') }}" value="{{ $line['options'] }}"></td>
                                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ ($count+10000) }}][options_values]" placeholder="{{ trans('supplier.valores_opcoes') }}" value="{{ $line['options_values'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                    <td style="min-width: 125px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ ($count+10000) }}][price]" placeholder="{{ trans('supplier.money_price') }}" value="{{ $line['price'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                    <td style="min-width: 150px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ ($count+10000) }}][sku]" placeholder="{{ trans('supplier.sku') }}" value="{{ $line['sku'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                    <td style="min-width: 100px"><input type="number" class="form-control form-control-sm variant_only_input" name="products[{{ ($count+10000) }}][weight_in_grams]" placeholder="Peso" value="{{ $line['weight_in_grams'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                    <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ ($count+10000) }}][width]" placeholder="Largura" value="{{ $line['width'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                    <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ ($count+10000) }}][height]" placeholder="Altura" value="{{ $line['height'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                    <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ ($count+10000) }}][depth]" placeholder="Profundidade" value="{{ $line['depth'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                    @if(old('products'))
                        @foreach(old('products') as $count => $line)
                            <tr count="{{ $count }}">
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeLine(this)"><i class="fas fa-times"></i></button>
                                </td>
                                <td style="min-width: 130px">
                                    <select name="products[{{ $count }}][type]" class="form-control form-control-sm" onchange="changeType(this)">
                                        <option value="product" {{ $line['type'] == 'product' ? 'selected' : '' }}>{{ trans('supplier.product') }}</option>
                                        <option value="variant" {{ $line['type'] == 'variant' ? 'selected' : '' }}>{{ trans('supplier.variante') }}</option>
                                    </select>
                                </td>
                                <td style="min-width: 200px" id="identifier_td">
                                    <input type="text" class="form-control form-control-sm" name="products[{{ $count }}][identifier]" placeholder="{{ trans('supplier.identificador') }}" onchange="updateIdentifier(this)" value="{{ $line['identifier'] }}">
                                </td>
                                <td style="min-width: 130px">
                                    <select name="products[{{ $count }}][category_id]" class="form-control form-control-sm">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $line['category_id'] == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][title]" placeholder="{{ trans('supplier.tittle') }}" value="{{ $line['title'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][ncm]" placeholder="{{ trans('supplier.ncm') }}" value="{{ $line['ncm'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][description]" placeholder="{{ trans('supplier.description') }}" value="{{ $line['description'] }}"></td>
                                <td style="min-width: 110px">
                                    <select name="products[{{ $count }}][public]" class="form-control form-control-sm">
                                        <option value="1" {{ $line['public'] == 1 ? 'selected' : '' }}>{{ trans('supplier.sim') }}</option>
                                        <option value="0" {{ $line['public'] == 0 ? 'selected' : '' }}>{{ trans('supplier.nao') }}</option>
                                    </select>
                                </td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][options]" placeholder="{{ trans('supplier.options') }}" value="{{ $line['options'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ $count }}][options_values]" placeholder="{{ trans('supplier.valores_opcoes') }}" value="{{ $line['options_values'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 125px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ $count }}][price]" placeholder="{{ trans('supplier.money_price') }}" value="{{ $line['price'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 150px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ $count }}][sku]" placeholder="{{ trans('supplier.sku') }}" value="{{ $line['sku'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 100px"><input type="number" class="form-control form-control-sm variant_only_input" name="products[{{ $count }}][weight_in_grams]" placeholder="Peso" value="{{ $line['weight_in_grams'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ $count }}][width]" placeholder="Largura" value="{{ $line['width'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ $count }}][height]" placeholder="Altura" value="{{ $line['height'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ $count }}][depth]" placeholder="Profundidade" value="{{ $line['depth'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </form>
        <div class="modal fade" role="dialog" tabindex="-1" id="modal-import-csv">
            <div class="modal-dialog modal-lg" role="document">
                <form method="POST" action="{{ route('admin.products.import_csv', $supplier_id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ trans('supplier.importar_csv') }}</h4>
                        </div>
                        <div class="modal-body">
                            <p>{{ trans('supplier.selecione_csv_importar') }}</p>
                            <div class="form-group">
                                <label>{{ trans('supplier.arquivo_csv') }}</label>
                                <input type="file" class="form-control" name="csv_file">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary">{{ trans('supplier.importar_csv') }}</button>
                            <button class="btn btn-secondary" data-dismiss="modal">{{ trans('supplier.sair') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(session('error'))
            <div class="modal" id="modal-error">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body bg-danger text-white">
                            {{ session('error') }}
                        </div>
                        <div class="modal-footer bg-danger">
                            <button class="btn btn-secondary" data-dismiss="modal">{{ trans('supplier.ok') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="d-none">
            <table>
                <tr id="example-product" count="0">
                    {{--<td style="min-width: 80px; padding: 5px" class="text-center">
                        <img src="http://localhost:8000/assets/img/products/eng-product-no-image.png" class="img-fluid" style="width:50px; height: 50px;">
                    </td>--}}
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeLine(this)"><i class="fas fa-times"></i></button>
                    </td>
                    <td style="min-width: 130px">
                        <select name="products[0][type]" class="form-control form-control-sm" onchange="changeType(this)">
                            <option value="product">{{ trans('supplier.product') }}</option>
                            <option value="variant">{{ trans('supplier.variante') }}</option>
                        </select>
                    </td>
                    <td style="min-width: 200px" id="identifier_td">
                        <input type="text" class="form-control form-control-sm" name="products[0][identifier]" placeholder="{{ trans('supplier.identificador') }}" onchange="updateIdentifier(this)">
                    </td>
                    <td style="min-width: 130px">
                        <select name="products[0][category_id]" class="form-control form-control-sm">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[0][title]" placeholder="{{ trans('supplier.tittle') }}"></td>
                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[0][ncm]" placeholder="{{ trans('supplier.ncm') }}"></td>
                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[0][description]" placeholder="{{ trans('supplier.description') }}"></td>
                    <td style="min-width: 110px">
                        <select name="products[0][public]" class="form-control form-control-sm">
                            <option value="1">{{ trans('supplier.sim') }}</option>
                            <option value="0">{{ trans('supplier.nao') }}</option>
                        </select>
                    </td>
                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[0][options]" placeholder="{{ trans('supplier.options') }}"></td>
                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[0][options_values]" placeholder="{{ trans('supplier.valores_opcoes') }}" readonly></td>
                    <td style="min-width: 125px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[0][price]" placeholder="{{ trans('supplier.money_price') }}" readonly></td>
                    <td style="min-width: 150px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[0][sku]" placeholder="{{ trans('supplier.sku') }}" readonly></td>
                    <td style="min-width: 100px"><input type="number" class="form-control form-control-sm variant_only_input" name="products[0][weight_in_grams]" placeholder="Peso" readonly></td>
                    <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[0][width]" placeholder="Largura" readonly></td>
                    <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[0][height]" placeholder="Altura" readonly></td>
                    <td style="min-width: 100px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[0][depth]" placeholder="Profundidade" readonly></td>
                    {{--<td style="min-width: 100px"><input type="file" style="font-size: 12px" name="products[0][photo]"></td>--}}
                </tr>
            </table>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js" type="text/javascript"></script>
        <script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>

        <script>
            let identifiers = {};
            let count = {{ old('products') ? count(old('products')) : 0 }};
            count = count + {{ isset($csv_products) ? count($csv_products) : 0 }};

            $(".decimal").maskMoney({thousands:''});

            function changeType(obj){
                let type = $(obj).val();

                if(type == 'product'){
                    $(obj).closest('tr').find('.variant_only_input').attr('readonly', true);
                    $(obj).closest('tr').find('.variant_only_input').val('');
                    $(obj).closest('tr').find('.product_only_input').removeAttr('readonly');
                }else{
                    $(obj).closest('tr').find('.product_only_input').attr('readonly', true);
                    $(obj).closest('tr').find('.variant_only_input').removeAttr('readonly');
                }
            }

            $.each($('.checkType'), function(index, obj){
                changeType($(obj).find('select:first'));
            })

            function addNewProduct(){
                let clone = $("#example-product").clone();

                clone.removeAttr('id');
                clone.attr('count', count);
                clone.find('input').each(function(index, obj){
                   let new_attr = $(obj).attr('name').replace('products[0]', 'products['+count+']');

                   $(obj).attr('name', new_attr);
                   $(obj).val('');
                });

                clone.find('select').each(function(index, obj){
                    let new_attr = $(obj).attr('name').replace('products[0]', 'products['+count+']');

                    $(obj).attr('name', new_attr);
                });

                $("#products-table").append(clone);
                $(".decimal").maskMoney({thousands:''});

                changeType(clone.find('select:first'));

                count++;
            }

            function updateIdentifier(obj){
                let identifier = $(obj).val();
                let count = $(obj).closest('tr').attr('count');

                identifiers[count] = identifier;
            }

            function removeLine(obj){
                $(obj).closest('tr').remove();
            }

            addNewProduct();

            @if(session('error'))
                $("#modal-error").modal('show');
            @endif
        </script>
    </body>
</html>
