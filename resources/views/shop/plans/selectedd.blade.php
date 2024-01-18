@extends('shop.layout.default')

@section('title', config('app.name').' - Planos Lojista')

@section('content')
    <!-- Header -->
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
            </div>
        </div>
    </div>
    <div class="container-fluid mt--7">
        <div class="row mb-4">
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{$plano->titulo}}</h3>                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{route('shop.plans.store')}}" id='form-store-plan'>
                            @csrf
                            <input type="hidden" name="plan_id" value='{{$plano->Id}}'>
                            <input type="hidden" name="email" value='{{$authenticated_user->email}}'>
                            <input type="hidden" name="payment_method" value='2'>
                            <input type="hidden" name="coupon_code" id='coupon_code'>

                            <div class="form-group" id='content-selected-plan'>
                                <p>
                                    {{$plano->titulo}}<br>
                                    <b>R$ {{number_format($plano->valor,2,",",".")}} / {{$plano->ciclo}}</b>
                                    <br>
                                </p>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label">Nome da Loja</label>
                                        <input type="text" class="form-control" name="name" placeholder="Nome da sua empresa" value="{{ $authenticated_user->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Seu nome</label>
                                        <input type="text" class="form-control" name="responsible_name" placeholder="Nome do responsável pela empresa" value="{{ $authenticated_user->responsible_name }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Email</label>
                                        <input type="email" class="form-control" placeholder="Email" value="{{ $authenticated_user->email }}" readonly>
                                    </div>
                                    <div class="form-group company_fields" style="display:none">
                                        <label class="control-label">Nome Fantasia</label>
                                        <input type="text" class="form-control" name="fantasy_name" placeholder="Nome Fantasia" value="{{ $authenticated_user->fantasy_name }}">
                                    </div>
                                    <div class="form-group company_fields" style="display:none">
                                        <label class="control-label">Razão Social</label>
                                        <input type="text" class="form-control" name="corporate_name" placeholder="Razão Social" value="{{ $authenticated_user->corporate_name }}">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label">Telefone</label>
                                        <input type="text" class="form-control phone" name="phone" placeholder="(11) 11111-1111" value="{{ $authenticated_user->phone }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Tipo de Documento</label>
                                        <select class="form-control" id="document_type">
                                            <option value="1" {{ strlen($authenticated_user->document) == 11 ? 'selected' : '' }}>CPF</option>
                                            <option value="2" {{ strlen($authenticated_user->document) != 11 ? 'selected' : '' }}>CNPJ</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" id="document_label">CNPJ</label>
                                        <input type="text" class="form-control" name="document" id="document" placeholder="Número do Documento" value="{{ $authenticated_user->document }}" required>
                                        <small class="field_error text-danger" style="display:none">Documento inválido.</small>
                                    </div>
                                    <div class="form-group company_fields" style="display:none">
                                        <label class="control-label">Inscrição Estadual</label>
                                        <input type="text" class="form-control" name="state_registration" placeholder="Inscrição Estadual" value="{{ $authenticated_user->state_registration }}">
                                    </div>
                                </div>
                            </div>
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
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label">CEP</label>
                                        <input type="text" class="form-control cep" name="zipcode" placeholder="11111-111" value="{{ $authenticated_user->address ? $authenticated_user->address->zipcode : '' }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">País</label>
                                        <select class="form-control" name="country" id="country_select" required>
                                            <option value="Brazil" {{ !$authenticated_user->address || ($authenticated_user->address && $authenticated_user->address->country == 'Brasil') ? 'selected' : '' }}>Brasil</option>
                                        </select>
                                    </div>                                
                                </div>
                                <div class="col">
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
                            <div class="row mt-3">
                                <div class="col">
                                    <h3>Dados de Pagamento</h3>
                                </div>                            
                            </div>
                            <div class="row">
                                @if($authenticated_user->token_card)
                                    <div class="col">
                                        <div class="form-group">
                                            <p>Você já forneceu os dados de pagamento. Caso queira alterar os dados do cartão, vá até <b><a href='{{route('shop.settings.index')}}'>Configurações</b></a> e remova o cartão antigo, após isso basta voltar aqui e fornecer os dados do novo cartão de crédito.</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="col">
                                        <div class="form-group">
                                            <label class="control-label">Nome no Cartão de Crédito </label>
                                            <input type="text" class="form-control" name="holder" placeholder="Nome no Cartão de Crédito" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Data de Expiração</label>
                                            <input type="text" class="form-control" name="expiration_date" id='expiration_date' placeholder="Data de Expiração MM/YYYY" required>
                                        </div>                          
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label class="control-label">Número do Cartão</label>
                                            <input type="text" class="form-control" name="card_number" placeholder="Número do Cartão" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Código de Segurança</label>
                                            <input type="text" class="form-control" name="security_code" placeholder="Código de Segurança do Cartão" required>
                                        </div>
                                    </div>
                                @endif                                
                            </div>
                            {!! $codeSelectedPlan !!}
                            <div class="row justify-content-end">
                                <div class="col-md-6 mt-4">
                                    <div class="row w-100">
                                        <div class="col-md-7">
                                            <div class="form-group mb-3">
                                                <div class="input-group input-group-alternative">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-money-coins"></i></span>
                                                    </div>
                                                    <input class="form-control" placeholder="Cupom de desconto" type="text" name="coupon">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 align-items-start">
                                            <a href='javascript:void(0);' style='color: #fff !important;' class='btn btn-danger' id='get-coupon'>Aplicar Cupom</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-100 d-flex flex-wrap align-items-center justify-content-end">
                                <div class="form-group text-center">
                                    <a href='{{route('shop.plans.index')}}' class="btn btn-danger" style="color: #fff;"><i class="fas fa-times"></i> Voltar</a>
                                    <button class="btn btn-success" type='submit' id='btn-assinar-plano' style="color: #fff;"><i class="fas fa-shopping-cart"></i> Assinar Plano</button>
                                </div>
                            </div>
                        </form>                       
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $("form").on("submit", function () {
                $(this).find(":submit").prop("disabled", true);
            });
        })
    </script>

    <script>
        $("#expiration_date").mask('99/9999');

        const installmentSemiannual = ['R$ 539,40', 'R$ 278,60', 'R$ 185,73', 'R$ 139,30', 'R$ 111,44', 'R$ 92,87']
        let installmentAnnual = ['R$ 923,70', 'R$ 477,09', 'R$ 318,06', 'R$ 238,55', 'R$ 190,84', 'R$ 159,03', 'R$ 136,97', 'R$ 119,85', 'R$ 106,53', 'R$ 95,88', 'R$ 87,16', 'R$ 79,90']

        $(document).on('change','#select-installment-plan-semiannual', function(){
            $('#value-plan-semiannual').html('<h3>'+$(this).val()+'x de <b>'+installmentSemiannual[$(this).val() - 1]+'</b></h3>')
            number_installments = $(this).val()
        })

        $(document).on('change','#select-installment-plan-annual', function(){
            $('#value-plan-annual').html('<h3>'+$(this).val()+'x de <b>'+installmentAnnual[$(this).val() - 1]+'</b></h3>')            
            number_installments = $(this).val()
        })

        $("#document_type").on('change', function(){
            change_doc_type();
        });

        $('#get-coupon').on('click', function(){
            let coupon = $("input[name=coupon]").val()
            $.ajax({
                url: '{{ route("shop.coupon.subscription.isvalid") }}',
                method: 'GET',
                data: { coupon, selectedPlan: {{$plano->Id}} },
                beforeSend: function(){
                    //$("#installment-plans").html("<img style='height: 80px;' src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}'>")
                },
                success: function(response){
                    console.log(response.coupon.id)
                    $('#content-selected-plan b').html(response.content_selected_plan_text)
                    installmentAnnual = response.installmentsDiscount                        
                    $('#value-plan-annual').html(response.value)
                    $('#select-installment-plan-annual option:eq(0)').prop('selected', true)
                    $('#coupon_code').val(response.coupon.id)
                    //$("#installment-plans").html(response)
                },
                error: function(response){
                    
                }
            });
        })

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

