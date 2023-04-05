@extends('admin.layout.default')

@section('title', 'Configurar Conta Plano')

@section('content')
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">

            </div>
        </div>
    </div>
    
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-2">
            <form method="POST" action="{{ route('admin.plans.payconfig_update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')                                        

                @if(($admins->plano_f == 2) or ($admins->plano_shop == 2))
                <div class="card bg-secondary shadow mt-4">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ trans('supplier.conta_bancara_safe2pay') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.metodo_recebimento_pagamento') }}</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="Safe2Pay">{{ trans('supplier.safe2pay') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.nome_responsavel_tecnico') }}</label>
                                    <input type="text" class="form-control" name="tech_name" id="tech_name" placeholder="{{ trans('supplier.nome_responsavel_tecnico_safe2pay') }}" value="{{ $authenticated_user->tech_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.cpf_responsavel_tecnico') }}</label>
                                    <input type="text" class="form-control" name="tech_document" id="tech_document" placeholder="{{ trans('supplier.documento_responsavel_conta_safe2pay') }}" value="{{ $authenticated_user->tech_document }}" required>
                                    <small class="field_error text-danger" style="display: none">{{ trans('supplier.cpf_invalido') }}</small>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.email_reponsavel_tecnico') }}</label>
                                    <input type="text" class="form-control" name="tech_email" id="tech_email" placeholder="{{ trans('supplier.email_responsavel_tecnico_safe2pay') }}" value="{{ $authenticated_user->tech_email }}" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.banco') }}</label>
                                    <select class="form-control" name="bank[code]" id="bank_list" required>

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Tipo de Conta</label>
                                    <select class="form-control" name="bank[account_type]" required>
                                        <option value="cc" {{ $authenticated_user->bank && $authenticated_user->bank->account_type == 'cc' ? 'selected' : '' }}>{{ trans('supplier.conta_corrente') }}</option>
                                        <option value="pp" {{ $authenticated_user->bank && $authenticated_user->bank->account_type == 'pp' ? 'selected' : '' }}>{{ trans('supplier.conta_poupanca') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.agencia') }}</label>
                                    <div class="row">
                                        <div class="col-lg-9 col-12">
                                            <input type="text" class="form-control" name="bank[agency]" placeholder="{{ trans('supplier.agencia_bancaria') }}" value="{{ $authenticated_user->bank ? $authenticated_user->bank->agency : '' }}" required>
                                        </div>
                                        <div class="col-lg-3 col-12">
                                            <input type="text" class="form-control" name="bank[agency_digit]" placeholder="{{ trans('supplier.digito') }}" value="{{ $authenticated_user->bank ? $authenticated_user->bank->agency_digit : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.conta') }}</label>
                                    <div class="row">
                                        <div class="col-lg-9 col-12">
                                            <input type="text" class="form-control" name="bank[account]" placeholder="{{ trans('supplier.conta_bancaria') }}" value="{{ $authenticated_user->bank ? $authenticated_user->bank->account : '' }}" required>
                                        </div>
                                        <div class="col-lg-3 col-12">
                                            <input type="text" class="form-control" name="bank[account_digit]" placeholder="{{ trans('supplier.digito') }}" value="{{ $authenticated_user->bank ? $authenticated_user->bank->account_digit : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                @if(($admins->plano_f == 1) or ($admins->plano_shop == 1))
                <div class="card bg-secondary shadow mt-4">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ trans('supplier.gerencianet') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.metodo_recebimento_pagamento') }}</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="Safe2Pay">{{ trans('supplier.gerencianet') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.cliente_id') }}</label>
                                    <input type="text" class="form-control" name="geren_cliente_id" id="geren_cliente_id" placeholder="Seu codigo client id" value="{{ $authenticated_user->geren_cliente_id  }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.cliente_secret') }}</label>
                                    <input type="text" class="form-control" name="geren_cliente_se" id="geren_cliente_se" placeholder="Seu codigo secret id" value="{{ $authenticated_user->geren_cliente_se }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.chave_pix') }}</label>
                                    <input type="text" class="form-control" name="geren_chave" id="geren_chave" placeholder="{{ trans('supplier.chave_pix') }}" value="{{ $authenticated_user->geren_chave }}" required>
                                </div>
                                

                                <div class="form-group">        
                                    <label for="files">certficado GerenciaNet </label>
                                    <input type="file" class="form-control" id="geren_pem" name="geren_pem"  />
                                   <output class="row mt-4" id="result" />
                                </div>
                                

                            </div>
                            
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="form-group text-right mt-4">
                    <button class="btn btn-lg btn-primary">{{ trans('supplier.atualizar_meus_dados') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $("#document").mask('00.000.000/0000-00');
        $("#responsible_document").mask('000.000.000-00');
        $("#tech_document").mask('000.000.000-00');

        /*$("#country_select").on('change', function(){
            change_contry();
        });*/

        $("#state_select").on('change', function(){
            $("#state").val($("#state_select").val());
            change_state();
        });

        $("#shipment_state_select").on('change', function(){
            $("#shipment_state").val($("#shipment_state_select").val());
            change_shipment_state();
        });

        $("#responsible_name").on('focusout', function(){
            let name = $(this).val();

            if($("#tech_name").val() == ''){
                $("#tech_name").val(name);
            }
        });

        $("#document").on('focusout', function(){
            let document = $(this).val();

            if(validarCNPJ(document)){
                $(this).parent().find('.field_error').hide();
            }else{
                $(this).parent().find('.field_error').show();
            }
        });

        $("#responsible_document").on('focusout', function(){
            let document = $(this).val();

            if(validarCPF(document)){
                if($("#tech_document").val() == ''){
                    $("#tech_document").val(document);
                }
                $(this).parent().find('.field_error').hide();
            }else{
                $(this).parent().find('.field_error').show();
            }
        });

        $("#tech_document").on('focusout', function(){
            let document = $(this).val();

            if(validarCPF(document)){
                $(this).parent().find('.field_error').hide();
            }else{
                $(this).parent().find('.field_error').show();
            }
        });

        if($("#tech_email").val() == ''){
            $("#tech_email").val($("#email").val());
        }

        function validarCNPJ(cnpj) {
            cnpj = cnpj.replace(/[^\d]+/g,'');

            if(cnpj == '') return false;

            if (cnpj.length != 14)
                return false;

            // Elimina CNPJs invalidos conhecidos
            if (cnpj == "00000000000000" ||
                cnpj == "11111111111111" ||
                cnpj == "22222222222222" ||
                cnpj == "33333333333333" ||
                cnpj == "44444444444444" ||
                cnpj == "55555555555555" ||
                cnpj == "66666666666666" ||
                cnpj == "77777777777777" ||
                cnpj == "88888888888888" ||
                cnpj == "99999999999999")
                return false;

            // Valida DVs
            tamanho = cnpj.length - 2
            numeros = cnpj.substring(0,tamanho);
            digitos = cnpj.substring(tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2)
                    pos = 9;
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(0))
                return false;

            tamanho = tamanho + 1;
            numeros = cnpj.substring(0,tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2)
                    pos = 9;
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(1))
                return false;

            return true;
        }

        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g,'');
            if(cpf == '') return false;
            // Elimina CPFs invalidos conhecidos
            if (cpf.length != 11 ||
                cpf == "00000000000" ||
                cpf == "11111111111" ||
                cpf == "22222222222" ||
                cpf == "33333333333" ||
                cpf == "44444444444" ||
                cpf == "55555555555" ||
                cpf == "66666666666" ||
                cpf == "77777777777" ||
                cpf == "88888888888" ||
                cpf == "99999999999")
                return false;
            // Valida 1o digito
            add = 0;
            for (i=0; i < 9; i ++)
                add += parseInt(cpf.charAt(i)) * (10 - i);
            rev = 11 - (add % 11);
            if (rev == 10 || rev == 11)
                rev = 0;
            if (rev != parseInt(cpf.charAt(9)))
                return false;
            // Valida 2o digito
            add = 0;
            for (i = 0; i < 10; i ++)
                add += parseInt(cpf.charAt(i)) * (11 - i);
            rev = 11 - (add % 11);
            if (rev == 10 || rev == 11)
                rev = 0;
            if (rev != parseInt(cpf.charAt(10)))
                return false;
            return true;
        }

        /*$("#city_select").on('change', function(){
            $("#city").val($("#city_select").val());
        });*/

        /*function change_contry(){
            let country = $('#country_select').val();

            if(country == 'Brasil'){
                $("#contry_div").hide();
                $("#state_div").hide();
                $("#city_div").hide();
                $("#country").val('Brasil');

                fillBrazilStates();
            }else{
                $("#contry_div").show();
                $("#state_div").show();
                $("#city_div").show();
                $("#country").val('');
            }
        }*/

        function change_state(){
            let uf = $("#state_select").val();

            fillBrazilCities(uf);
        }

        function change_shipment_state(){
            let uf = $("#shipment_state_select").val();

            fillShipmentBrazilCities(uf);
        }

        function fillBrazilStates(){
            let brazil_states = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'RR', 'SC', 'SP', 'SE', 'TO'];
            let current_state = $("#state_select").attr('state');

            $("#state_select").html('<option value="" selected>Selecione um estado</option>');

            $.each(brazil_states, function(index, state){
                if(state == current_state){
                    $("#state_select").append('<option value="'+ state +'" selected>'+ state +'</option>');
                    fillBrazilCities(current_state);
                }else{
                    $("#state_select").append('<option value="'+ state +'">'+ state +'</option>');
                }
            });
        }

        function fillShipmentBrazilStates(){
            let brazil_states = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'RR', 'SC', 'SP', 'SE', 'TO'];
            let current_state = $("#shipment_state_select").attr('state');

            $("#shipment_state_select").html('<option value="" selected>Selecione um estado</option>');

            $.each(brazil_states, function(index, state){
                if(state == current_state){
                    $("#shipment_state_select").append('<option value="'+ state +'" selected>'+ state +'</option>');
                    fillShipmentBrazilCities(current_state);
                }else{
                    $("#shipment_state_select").append('<option value="'+ state +'">'+ state +'</option>');
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
                    //console.log(response);
                }
            });
        }

        function fillShipmentBrazilCities(uf){
            let current_city = $("#shipment_city_select").attr('city');

            $.ajax({
                url: '{{ route("api.cities") }}',
                method: 'GET',
                data: { uf : uf },
                beforeSend: function(){
                    $("#shipment_city_select").html('<option value="" selected>Carregando...</option>');
                },
                success: function(cities){
                    $("#shipment_city_select").html('<option value="" selected>Selecione uma cidade</option>');

                    $.each(cities, function(index, city){
                        if(city.name == current_city){
                            $("#shipment_city_select").append('<option value="'+ city.name +'" selected>'+ city.name +'</option>');
                        }else{
                            $("#shipment_city_select").append('<option value="'+ city.name +'">'+ city.name +'</option>');
                        }
                    });
                },
                error: function(response){
                    //(response);
                }
            });
        }

        fillBrazilStates();
        fillShipmentBrazilStates();

        var bank_code = '{!! $authenticated_user->bank ? $authenticated_user->bank->code : "000" !!}';

        $.ajax({
            url: '/js/banklist.json',
            success: function(banks){
                $.each(banks, function(index, bank){
                    if(bank.value == bank_code){
                        $("#bank_list").append('<option value="'+bank.value+'" selected>'+bank.value+' - '+bank.label+'</option>');
                    }else{
                        $("#bank_list").append('<option value="'+bank.value+'">'+bank.value+' - '+bank.label+'</option>');
                    }
                });
            }
        })
    </script>
@endsection