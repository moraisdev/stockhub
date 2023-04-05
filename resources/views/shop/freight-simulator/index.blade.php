@extends('shop.layout.default')

@section('title', config('app.name').' - Simulador de Frete')

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
            <div class="col-md-5 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Simulador de frete</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">                        
                        <div class="col-md-12">                                
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.estado') }}</label>
                                <select class="form-control" name="state_code" id="state_select" >
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.city') }}</label>
                                <select class="form-control" name="city" id="city_select">
                                    <option value="">{{ trans('supplier.selecione_estado_primeiro') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="bairro">Informe o bairro ou nome da rua</label>
                                <input type="text" name="district" id="district" placeholder="Bairro ou rua" class='form-control'>
                            </div>
                            <div class="form-group">
                                <button class='btn btn-primary' id='btn-search-zipcode'>Buscar CEP</button>
                            </div>
                            <div id="result-search-zipcode"></div>
                            <hr>
                            <p>Caso já saiba o CEP de destino, basta digitá-lo abaixo</p>
                            <div class="form-group">
                                <label for="estado">Informe o CEP</label>
                                <input type="text" name="cep" id="cep" placeholder="{{ trans('supplier.postal_code') }}" class='form-control cep' required>
                            </div>
                            <div class="form-group">                                
                                <label for="produtos">Selecione o produto</label>
                                <select name="produtos" id="produtos" class='form-control'>                                        
                                    @forelse ($products as $product)
                                        @if($product->supplier->shipping_method == 'melhor_envio')
                                            <option value='{{$product->id}}'>{{$product->title}}</option>
                                        @endif
                                    @empty
                                        <option>Nenhum produto encontrado</option>
                                    @endforelse
                                    
                                </select>
                            </div>
                            <div class="form-group">
                                <button id='btn-quote' class='btn btn-success'>Simular Frete</button>
                            </div>
                            <div id="result-simulate-quote"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).on('click', '.btn-cp-zipcode', function(){
            $('#cep').val($(this).attr('value'))
            $('#produtos').focus()
        })

        $('#btn-quote').on('click', function(){
            let to_zipcode = $('#cep').val()
            let product = $('#produtos').val()

            $.ajax({
                url: '{{ route("shop.freight-simulator.simulate") }}',
                method: 'GET',
                data: { to_zipcode, product },
                beforeSend: function(){
                    $("#result-simulate-quote").html('<hr><p>Realizando simulação do frete...</p>');
                },
                success: function(response){
                    $("#result-simulate-quote").html(response);
                    $.each(response, function(index, quote){
                        $("#result-simulate-quote").append(
                            "<hr>"+ 
                            "<div class='col-md-12 mb-3'>"+                               
                                "<div class='col-xs-2'><img src='"+quote.company.picture+"' style='height: 30px;'></div>"+
                                "<div class='col-xs-10'>"+
                                    "<p>"+quote.name+" - <b>"+(quote.error ? quote.error : '')+(quote.price ? 'R$ '+quote.price : '')+"</b>"+
                                    "</p>"+
                                "</div>"+
                            "</div>")
                    });
                },
                error: function(response){
                    $("#result-simulate-quote").html('<hr><p>Não foi possível realizar a simulação</p>');
                }
            });
        })

        $('#btn-search-zipcode').on('click', function(){
            let state = $('#state_select option:checked').val()
            let city = $('#city_select option:checked').val()
            let district = $('#district').val()

            $.ajax({
                url: '{{ route("api.melhor_envio.get_zipcode") }}',
                method: 'GET',
                data: { state, city, district },
                beforeSend: function(){
                    $("#result-search-zipcode").html('<p>Carregando...</p>');
                },
                success: function(response){
                    $("#result-search-zipcode").html('');
                    $.each(response, function(index, cep){
                        $("#result-search-zipcode").append(
                            "<hr>"+ 
                            "<div class='col-md-12 mb-3'>"+                               
                                "<p for='cep_selected_"+index+"'>"+
                                "<button class='btn btn-primary btn-sm btn-cp-zipcode' value='"+cep.cep+"'><i class='far fa-copy'></i> Copiar CEP</button>"+
                                "<strong>"+cep.cep+"</strong><br><small>"+cep.logradouro+", "+cep.bairro+", "+cep.localidade+", "+cep.uf+"</small></p>"+                      
                            "</div>")
                    });
                },
                error: function(response){
                    $("#result-search-zipcode").html('<p>Nenhum CEP encontrado'+(!district ? '. Tente fornecer o bairro ou nome da rua.</p>' : ''));
                }
            });
        })

        function change_state(){
            let uf = $("#state_select").val();

            fillBrazilCities(uf);
        }

        function fillBrazilStates(){
            let brazil_states = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'RR', 'SC', 'SP', 'SE', 'TO'];
            let current_state = $("#state_select").attr('state');

            $("#state_select").html('<option value="" selected>Selecione um estado</option>');

            //console.log(current_state);

            $.each(brazil_states, function(index, state){
                if(state == current_state){
                    $("#state_select").append('<option value="'+ state +'" selected>'+ state +'</option>');
                    fillBrazilCities(current_state);
                }else{
                    $("#state_select").append('<option value="'+ state +'">'+ state +'</option>');
                }
            });
        }

        function fillBrazilCities(uf){
            let current_city = $("#city_select").attr('city');

            $.ajax({
                url: '{{ route("api.cities") }}',
                method: 'GET',
                data: { uf : uf },
                beforeSend: function(){
                    $("#city_select").html('<option value="" selected>Carregando...</option>');
                },
                success: function(cities){
                    $("#city_select").html('<option value="" selected>Selecione uma cidade</option>');

                    $.each(cities, function(index, city){
                        if(city.name == current_city){
                            $("#city_select").append('<option value="'+ city.name +'" selected>'+ city.name +'</option>');
                        }else{
                            $("#city_select").append('<option value="'+ city.name +'">'+ city.name +'</option>');
                        }
                    });
                },
                error: function(response){
                    console.log(response);
                }
            });
        }

        fillBrazilStates();

        $("#state_select").on('change', function(){
            $("#state").val($("#state_select").val());
            change_state();
        });
    </script>
@endsection
