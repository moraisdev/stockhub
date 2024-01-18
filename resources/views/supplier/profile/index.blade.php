@extends('supplier.layout.default')

@section('title', 'Meu perfil')

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px; background-image: url(https://wallpapertag.com/wallpaper/full/5/9/b/664802-vertical-flat-design-wallpapers-1920x1080.jpg); background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask {{env('PAINELCOR')}} opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1 class="display-2 text-white">Bem vindo, {{ $authenticated_user->name }}</h1>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-2">
            <form method="POST" action="{{ route('supplier.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Mantenha seus dados atualizados</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">Nome no {{config('app.name')}}</label>
                                    <input type="text" class="form-control" name="name" placeholder="Nome da sua empresa" value="{{ $authenticated_user->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="Email" value="{{ $authenticated_user->email }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Seu nome completo</label>
                                    <input type="text" class="form-control" name="responsible_name" id="responsible_name" placeholder="Nome completo do responsável da empresa" value="{{ $authenticated_user->responsible_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Seu CPF</label>
                                    <input type="text" class="form-control" name="responsible_document" id="responsible_document" placeholder="CPF do responsável pela empresa" value="{{ $authenticated_user->responsible_document }}" required>
                                    <small class="field_error text-danger" style="display: none">CPF inválido.</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">Nome Fantasia</label>
                                    <input type="text" class="form-control" name="commercial_name" placeholder="Nome fantasia de sua empresa" value="{{ $authenticated_user->commercial_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Razão Social</label>
                                    <input type="text" class="form-control" name="legal_name" placeholder="Razão Social de sua empresa" value="{{ $authenticated_user->legal_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">CNPJ</label>
                                    <input type="text" class="form-control" id="document" name="document" placeholder="Número do Documento" value="{{ $authenticated_user->document }}" required>
                                    <small class="field_error text-danger" style="display: none">CNPJ inválido.</small>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Telefone</label>
                                    <input type="text" class="form-control phone" name="phone" placeholder="(11) 11111-1111" value="{{ $authenticated_user->phone }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-secondary shadow mt-4">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Endereço</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">Logradouro</label>
                                    <input type="text" class="form-control" name="street" placeholder="Logradouro" value="{{ $authenticated_user->address ? $authenticated_user->address->street : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Número</label>
                                    <input type="text" class="form-control" name="number" placeholder="Número" value="{{ $authenticated_user->address ? $authenticated_user->address->number : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Bairro</label>
                                    <input type="text" class="form-control" name="district" placeholder="Bairro" value="{{ $authenticated_user->address ? $authenticated_user->address->district : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Complemento</label>
                                    <input type="text" class="form-control" name="complement" placeholder="Complemento" value="{{ $authenticated_user->address ? $authenticated_user->address->complement : '' }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">CEP</label>
                                    <input type="text" class="form-control cep" name="zipcode" placeholder="11111-111" value="{{ $authenticated_user->address ? $authenticated_user->address->zipcode : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">País</label>
                                    <select class="form-control" name="country" id="country_select" required>
                                        <option value="Brasil" {{ !$authenticated_user->address || ($authenticated_user->address && $authenticated_user->address->country == 'Brasil') ? 'selected' : '' }}>Brasil</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Estado</label>
                                    <select class="form-control" name="state_code" id="state_select" state="{{ $authenticated_user->address ? $authenticated_user->address->state_code : '' }}" required>
                                        <option value="">Selecione o país primeiro</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Cidade</label>
                                    <select class="form-control" name="city" id="city_select" city="{{ $authenticated_user->address ? $authenticated_user->address->city : '' }}" required>
                                        <option value="">Selecione o estado primeiro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($authenticated_user->use_shipment_address)
                <div class="card bg-secondary shadow mt-4">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Endereço de Remessa</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">Logradouro</label>
                                    <input type="text" class="form-control" name="shipment[street]" placeholder="Logradouro" value="{{ $authenticated_user->shipment_address ? $authenticated_user->shipment_address->street : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Número</label>
                                    <input type="text" class="form-control" name="shipment[number]" placeholder="Número" value="{{ $authenticated_user->shipment_address ? $authenticated_user->shipment_address->number : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Bairro</label>
                                    <input type="text" class="form-control" name="shipment[district]" placeholder="Bairro" value="{{ $authenticated_user->shipment_address ? $authenticated_user->shipment_address->district : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Complemento</label>
                                    <input type="text" class="form-control" name="shipment[complement]" placeholder="Complemento" value="{{ $authenticated_user->shipment_address ? $authenticated_user->shipment_address->complement : '' }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">CEP</label>
                                    <input type="text" class="form-control cep" name="shipment[zipcode]" placeholder="11111-111" value="{{ $authenticated_user->shipment_address ? $authenticated_user->shipment_address->zipcode : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">País</label>
                                    <select class="form-control" name="shipment[country]" id="shipment_country_select" required>
                                        <option value="Brasil" {{ !$authenticated_user->shipment_address || ($authenticated_user->shipment_address && $authenticated_user->shipment_address->country == 'Brasil') ? 'selected' : '' }}>Brasil</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Estado</label>
                                    <select class="form-control" name="shipment[state_code]" id="shipment_state_select" state="{{ $authenticated_user->shipment_address ? $authenticated_user->shipment_address->state_code : '' }}" required>
                                        <option value="">Selecione o país primeiro</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Cidade</label>
                                    <select class="form-control" name="shipment[city]" id="shipment_city_select" city="{{ $authenticated_user->shipment_address ? $authenticated_user->shipment_address->city : '' }}" required>
                                        <option value="">Selecione o estado primeiro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card bg-secondary shadow mt-4">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Safe2Pay e Conta Bancária</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">Método de recebimento/pagamento</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="Safe2Pay">Safe2Pay</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Nome do responsável técnico</label>
                                    <input type="text" class="form-control" name="tech_name" id="tech_name" placeholder="Nome do responsável técnico pela sua conta Safe2Pay" value="{{ $authenticated_user->tech_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">CPF do responsável técnico</label>
                                    <input type="text" class="form-control" name="tech_document" id="tech_document" placeholder="Documento do responsável técnico pela sua conta Safe2Pay" value="{{ $authenticated_user->tech_document }}" required>
                                    <small class="field_error text-danger" style="display: none">CPF inválido.</small>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">E-mail do responsável técnico</label>
                                    <input type="text" class="form-control" name="tech_email" id="tech_email" placeholder="E-mail do responsável técnico pela sua conta Safe2Pay" value="{{ $authenticated_user->tech_email }}" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">Banco</label>
                                    <select class="form-control" name="bank[code]" id="bank_list" required>

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Tipo de Conta</label>
                                    <select class="form-control" name="bank[account_type]" required>
                                        <option value="cc" {{ $authenticated_user->bank && $authenticated_user->bank->account_type == 'cc' ? 'selected' : '' }}>Conta Corrente</option>
                                        <option value="pp" {{ $authenticated_user->bank && $authenticated_user->bank->account_type == 'pp' ? 'selected' : '' }}>Conta Poupança</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Agência</label>
                                    <div class="row">
                                        <div class="col-lg-9 col-12">
                                            <input type="text" class="form-control" name="bank[agency]" placeholder="Agência bancária" value="{{ $authenticated_user->bank ? $authenticated_user->bank->agency : '' }}" required>
                                        </div>
                                        <div class="col-lg-3 col-12">
                                            <input type="text" class="form-control" name="bank[agency_digit]" placeholder="Dígito" value="{{ $authenticated_user->bank ? $authenticated_user->bank->agency_digit : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Conta</label>
                                    <div class="row">
                                        <div class="col-lg-9 col-12">
                                            <input type="text" class="form-control" name="bank[account]" placeholder="Conta bancária" value="{{ $authenticated_user->bank ? $authenticated_user->bank->account : '' }}" required>
                                        </div>
                                        <div class="col-lg-3 col-12">
                                            <input type="text" class="form-control" name="bank[account_digit]" placeholder="Dígito" value="{{ $authenticated_user->bank ? $authenticated_user->bank->account_digit : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-secondary shadow mt-4">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">GerenciaNet</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label class="control-label">Método de recebimento/pagamento</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="Safe2Pay">GerenciaNet</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Cliente ID</label>
                                    <input type="text" class="form-control" name="geren_cliente_id" id="geren_cliente_id" placeholder="Seu codigo client id" value="{{ $authenticated_user->geren_cliente_id  }}">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Cliente Secret</label>
                                    <input type="text" class="form-control" name="geren_cliente_se" id="geren_cliente_se" placeholder="Seu codigo secret id" value="{{ $authenticated_user->geren_cliente_se }}">
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label">Chave PIX</label>
                                    <input type="text" class="form-control" name="geren_chave" id="geren_chave" placeholder="Sua Chave PIX" value="{{ $authenticated_user->geren_chave }}">
                                </div>
                                

                                <div class="form-group">        
                                    <label for="files">certficada GerenciaNet </label>
                                    <input type="file" class="form-control" id="geren_pem" name="geren_pem"  />
                                   <output class="row mt-4" id="result" />
                                </div>
                                

                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <div class="form-group text-right mt-4">
                    @if($authenticated_user->status == 'active')
                        <a href="{{ route('supplier.profile.toggle_status') }}" class="btn btn-lg btn-danger" tooltip="true" title="Pausar as atividades de sua conta. Retirando seus produtos do catálogo dos lojistas. Você pode reiniciar suas atividades a qualquer momento.">Pausar atividades</a>
                    @else
                        <a href="{{ route('supplier.profile.toggle_status') }}" class="btn btn-lg btn-success" tooltip="true" title="Reiniciar as atividades de sua conta. Seus produtos voltarão a aparecer no catálogo dos lojistas.">Reiniciar atividades</a>
                    @endif
                    <button class="btn btn-lg btn-primary">Atualizar meus dados</button>
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
