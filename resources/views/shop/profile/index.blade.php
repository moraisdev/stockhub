@extends('shop.layout.default')

@section('title', __('supplier.meu_perfil_title'))

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px; background-image: url(https://wallpapertag.com/wallpaper/full/5/9/b/664802-vertical-flat-design-wallpapers-1920x1080.jpg); background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask {{env('PAINELCOR')}} opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1 class="display-2 text-white">{{ trans('supplier.bem_vindo') }}, {{ $authenticated_user->name }}</h1>
                <a href="{{ route('shop.products.index') }}" class="btn btn-secondary">{{ trans('supplier.back') }}</a>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-2">
            <form method="POST" action="{{ route('shop.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ trans('supplier.mantenha_seus_dados_atualizados') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.name_store') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ trans('supplier.nome_empresa') }}" value="{{ $authenticated_user->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Nome Responsavel</label>
                                    <input type="text" class="form-control" name="responsible_name" placeholder="{{ trans('supplier.nome_responsavel_empresa') }}" value="{{ $authenticated_user->responsible_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.text_email') }}</label>
                                    <input type="email" class="form-control" placeholder="{{ trans('supplier.text_email') }}" value="{{ $authenticated_user->email }}" readonly>
                                </div>
                                <div class="form-group company_fields" style="display:none">
                                    <label class="control-label">{{ trans('supplier.fantasy_name') }}</label>
                                    <input type="text" class="form-control" name="fantasy_name" placeholder="{{ trans('supplier.fantasy_name') }}" value="{{ $authenticated_user->fantasy_name }}">
                                </div>
                                <div class="form-group company_fields" style="display:none">
                                    <label class="control-label">{{ trans('supplier.company_name') }}</label>
                                    <input type="text" class="form-control" name="corporate_name" placeholder="{{ trans('supplier.company_name') }}" value="{{ $authenticated_user->corporate_name }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.text_phone') }}</label>
                                    <input type="text" class="form-control phone" name="phone" placeholder="(11) 11111-1111" value="{{ $authenticated_user->phone }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Tipo de Documento</label>
                                    <select class="form-control" id="document_type">
                                        <option value="1" {{ strlen($authenticated_user->document) == 11 ? 'selected' : '' }}>CPF</option>
                                        <option value="2" {{ strlen($authenticated_user->document) != 11 ? 'selected' : '' }}>{{ trans('supplier.cnpj') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" id="document_label">{{ trans('supplier.cnpj') }}</label>
                                    <input type="text" class="form-control" name="document" id="document" placeholder="{{ trans('supplier.numero_documento') }}" value="{{ $authenticated_user->document }}" required>
                                    <small class="field_error text-danger" style="display:none">Documento inválido.</small>
                                </div>
                                <div class="form-group company_fields" style="display:none">
                                    <label class="control-label">{{ trans('supplier.state_registration') }}</label>
                                    <input type="text" class="form-control" name="state_registration" placeholder="{{ trans('supplier.state_registration') }}" value="{{ $authenticated_user->state_registration }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-secondary shadow mt-4">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ trans('supplier.adress') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.logradouro') }}</label>
                                    <input type="text" class="form-control" name="street" placeholder="{{ trans('supplier.logradouro') }}" value="{{ $authenticated_user->address ? $authenticated_user->address->street : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.numero') }}</label>
                                    <input type="text" class="form-control" name="number" placeholder="{{ trans('supplier.numero') }}" value="{{ $authenticated_user->address ? $authenticated_user->address->number : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.brotherhood') }}</label>
                                    <input type="text" class="form-control" name="district" placeholder="{{ trans('supplier.brotherhood') }}" value="{{ $authenticated_user->address ? $authenticated_user->address->district : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.complemment') }}</label>
                                    <input type="text" class="form-control" name="complement" placeholder="{{ trans('supplier.complemment') }}" value="{{ $authenticated_user->address ? $authenticated_user->address->complement : '' }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.postal_code') }}</label>
                                    <input type="text" class="form-control cep" name="zipcode" placeholder="11111-111" value="{{ $authenticated_user->address ? $authenticated_user->address->zipcode : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.pais') }}</label>
                                    <select class="form-control" name="country" id="country_select" required>
                                        <option value="Brazil" {{ !$authenticated_user->address || ($authenticated_user->address && $authenticated_user->address->country == 'Brasil') ? 'selected' : '' }}>Brasil</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.estado') }}</label>
                                    <select class="form-control" name="state_code" id="state_select" state="{{ $authenticated_user->address ? $authenticated_user->address->state_code : '' }}" required>
                                        <option value="">{{ trans('supplier.selecione_pais_primeiro') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ trans('supplier.city') }}</label>
                                    <select class="form-control" name="city" id="city_select" city="{{ $authenticated_user->address ? $authenticated_user->address->city : '' }}" required>
                                        <option value="">{{ trans('supplier.selecione_estado_primeiro') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right mt-2">
                    <button class="btn btn-lg btn-primary">{{ trans('supplier.atualizar_meus_dados') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $("#document_type").on('change', function(){
            change_doc_type();
        });

        function change_doc_type(){
            if($("#document_type").val() == 1){
                $("#document_label").html('CPF');
                $("#document").mask('000.000.000-00');
                $('.company_fields').hide();
            }else{
                $("#document_label").html('CNPJ');
                $("#document").mask('00.000.000/0000-00');
                $('.company_fields').show();
            }
        }

        change_doc_type();

        $("#document").on('focusout', function(){
            let document = $(this).val().replace(/[^\d]+/g,'');

            if(($("#document_type").val() == 1 && validarCPF(document)) || ($("#document_type").val() == 2 && validarCNPJ(document))){
                $(this).parent().find('.field_error').hide();
            }else{
                $(this).parent().find('.field_error').show();
            }
        });

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

        $("#state_select").on('change', function(){
            $("#state").val($("#state_select").val());
            change_state();
        });
    </script>
@endsection
