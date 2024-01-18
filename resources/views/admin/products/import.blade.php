<html>
    <head>
        <title>Cadastro rápido de produtos</title>
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
                    <h3 class="d-inline">Cadastro rápido de produtos</h3>
                    <div class="d-inline flex-grow-1">
                        <div class="float-right">
                            <a href="{{ asset('assets/img/import-example.png') }}" target="_blank" class="btn btn-warning btn-sm" {{--data-toggle="modal" data-target="#modal-example"--}}><i class="fas fa-info"></i> Ver modelo de exemplo</a>
                            <a href="{{ route('admin.products.update_descriptions', $supplier_id) }}" target="_blank" class="btn btn-primary btn-sm" {{--data-toggle="modal" data-target="#modal-example"--}}><i class="fas fa-box"></i> Atualizar descrições</a>
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-import-csv">
                                <i class="fa fa-file-excel-o"></i> Importar CSV
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="addNewProduct()"><i class="fas fa-plus"></i> Adicionar produto</button>
                            <button class="btn btn-primary btn-sm">Concluir Cadastro</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-default btn-sm">Voltar para o sistema</a>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered">
                <thead class="bg-white">
                    <tr>
                        <th></th>
                        <th>Tipo</th>
                        <th>Identificador</th>
                        <th>Categoria</th>
                        <th>Título</th>
                        <th>NCM</th>
                        <th>Descrição</th>
                        <th>Público?</th>
                        <th>Opções</th>
                        <th>Valores das opções</th>
                        <th>Preço</th>
                        <th>SKU</th>
                        <th>Peso (g)</th>
                        <th>Largura (cm)</th>
                        <th>Altura (cm)</th>
                        <th>Profundidade (cm)</th>
                        {{--<th>Foto</th>--}}
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
                                        <option value="product" {{ strtolower($line['type']) == 'produto' ? 'selected' : '' }}>Produto</option>
                                        <option value="variant" {{ strtolower($line['type']) == 'variante' ? 'selected' : '' }}>Variante</option>
                                    </select>
                                </td>
                                <td style="min-width: 200px" id="identifier_td">
                                    <input type="text" class="form-control form-control-sm" name="products[{{ $count }}][identifier]" placeholder="Identificador" onchange="updateIdentifier(this)" value="{{ strtolower($line['identifier']) }}">
                                </td>
                                <td style="min-width: 130px">
                                    <select name="products[{{ $count }}][category_id]" class="form-control form-control-sm">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ strtolower($line['category']) == strtolower($category->name) ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][title]" placeholder="Título" value="{{ $line['title'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][ncm]" placeholder="NCM" value="{{ $line['ncm'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][description]" placeholder="Descrição" value="{{ $line['description'] }}"></td>
                                <td style="min-width: 110px">
                                    <select name="products[{{ $count }}][public]" class="form-control form-control-sm">
                                        <option value="0" {{ (strtolower($line['public']) == 'nao' || strtolower($line['public']) == 'não') ? 'selected' : '' }}>Não</option>
                                        <option value="1" {{ (strtolower($line['public']) == 'sim') ? 'selected' : '' }}>Sim</option>
                                    </select>
                                </td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][options]" placeholder="Opções" value="{{ $line['options'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ $count }}][options_values]" placeholder="Valores das opções" value="{{ $line['options_values'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 125px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ $count }}][price]" placeholder="Preço" value="{{ $line['price'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 150px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ $count }}][sku]" placeholder="SKU" value="{{ $line['sku'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
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
                                            <option value="product">Produto</option>
                                            <option value="variant" selected>Variante</option>
                                        </select>
                                    </td>
                                    <td style="min-width: 200px" id="identifier_td">
                                        <input type="text" class="form-control form-control-sm" name="products[{{ ($count+10000) }}][identifier]" placeholder="Identificador" onchange="updateIdentifier(this)" value="{{ strtolower($line['identifier']) }}">
                                    </td>
                                    <td style="min-width: 130px">
                                        <select name="products[{{ ($count+10000) }}][category_id]" class="form-control form-control-sm">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ strtolower($line['category']) == strtolower($category->name) ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ ($count+10000) }}][title]" placeholder="Título" value="{{ $line['title'] }}"></td>
                                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ ($count+10000) }}][ncm]" placeholder="NCM" value="{{ $line['ncm'] }}"></td>
                                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ ($count+10000) }}][description]" placeholder="Descrição" value="{{ $line['description'] }}"></td>
                                    <td style="min-width: 110px">
                                        <select name="products[{{ ($count+10000) }}][public]" class="form-control form-control-sm">
                                            <option value="1" {{ (strtolower($line['public']) == 'sim') ? 'selected' : '' }}>Sim</option>
                                            <option value="0" {{ (strtolower($line['public']) == 'nao' || strtolower($line['public']) == 'não') ? 'selected' : '' }}>Não</option>
                                        </select>
                                    </td>
                                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ ($count+10000) }}][options]" placeholder="Opções" value="{{ $line['options'] }}"></td>
                                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ ($count+10000) }}][options_values]" placeholder="Valores das opções" value="{{ $line['options_values'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                    <td style="min-width: 125px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ ($count+10000) }}][price]" placeholder="Preço" value="{{ $line['price'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                    <td style="min-width: 150px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ ($count+10000) }}][sku]" placeholder="SKU" value="{{ $line['sku'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
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
                                        <option value="product" {{ $line['type'] == 'product' ? 'selected' : '' }}>Produto</option>
                                        <option value="variant" {{ $line['type'] == 'variant' ? 'selected' : '' }}>Variante</option>
                                    </select>
                                </td>
                                <td style="min-width: 200px" id="identifier_td">
                                    <input type="text" class="form-control form-control-sm" name="products[{{ $count }}][identifier]" placeholder="Identificador" onchange="updateIdentifier(this)" value="{{ $line['identifier'] }}">
                                </td>
                                <td style="min-width: 130px">
                                    <select name="products[{{ $count }}][category_id]" class="form-control form-control-sm">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $line['category_id'] == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][title]" placeholder="Título" value="{{ $line['title'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][ncm]" placeholder="NCM" value="{{ $line['ncm'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][description]" placeholder="Descrição" value="{{ $line['description'] }}"></td>
                                <td style="min-width: 110px">
                                    <select name="products[{{ $count }}][public]" class="form-control form-control-sm">
                                        <option value="1" {{ $line['public'] == 1 ? 'selected' : '' }}>Sim</option>
                                        <option value="0" {{ $line['public'] == 0 ? 'selected' : '' }}>Não</option>
                                    </select>
                                </td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[{{ $count }}][options]" placeholder="Opções" value="{{ $line['options'] }}"></td>
                                <td style="min-width: 200px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ $count }}][options_values]" placeholder="Valores das opções" value="{{ $line['options_values'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 125px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[{{ $count }}][price]" placeholder="Preço" value="{{ $line['price'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
                                <td style="min-width: 150px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[{{ $count }}][sku]" placeholder="SKU" value="{{ $line['sku'] }}" {{ $line['type'] == 'product' ? 'readonly' : '' }}></td>
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
                            <h4 class="modal-title">Importar produtos por CSV</h4>
                        </div>
                        <div class="modal-body">
                            <p>Selecione um arquivo CSV para importar os produtos para o cadastro rápido.</p>
                            <div class="form-group">
                                <label>Arquivo CSV</label>
                                <input type="file" class="form-control" name="csv_file">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary">Importar CSV</button>
                            <button class="btn btn-secondary" data-dismiss="modal">Sair</button>
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
                            <button class="btn btn-secondary" data-dismiss="modal">Ok</button>
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
                            <option value="product">Produto</option>
                            <option value="variant">Variante</option>
                        </select>
                    </td>
                    <td style="min-width: 200px" id="identifier_td">
                        <input type="text" class="form-control form-control-sm" name="products[0][identifier]" placeholder="Identificador" onchange="updateIdentifier(this)">
                    </td>
                    <td style="min-width: 130px">
                        <select name="products[0][category_id]" class="form-control form-control-sm">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[0][title]" placeholder="Título"></td>
                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[0][ncm]" placeholder="NCM"></td>
                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[0][description]" placeholder="Descrição"></td>
                    <td style="min-width: 110px">
                        <select name="products[0][public]" class="form-control form-control-sm">
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </td>
                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm" name="products[0][options]" placeholder="Opções"></td>
                    <td style="min-width: 200px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[0][options_values]" placeholder="Valores das opções" readonly></td>
                    <td style="min-width: 125px"><input type="text" class="form-control form-control-sm decimal variant_only_input" name="products[0][price]" placeholder="Preço" readonly></td>
                    <td style="min-width: 150px"><input type="text" class="form-control form-control-sm variant_only_input" name="products[0][sku]" placeholder="SKU" readonly></td>
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
