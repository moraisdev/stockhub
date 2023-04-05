@extends('shop.login.layout')

@section('content')
<style>
    .title-price-plan{
        font-size: 1.5rem;
        font-weight: 700;
        color: #0064a8;
    }

    .bg-plan-anual{
        /* background: linear-gradient(40deg, #2464a4 0, #2464a4 40%, #1e5b8e 100%) !important; */
        background: linear-gradient(40deg, #ff9000 0, #ff9000 40%, #ffb400 100%) !important;
    }

    .bg-plan-anual h1, .bg-plan-anual h3, .bg-plan-anual p, .bg-plan-anual small, .bg-plan-anual h4, .bg-plan-anual h2{
        color: #fff !important;
    }

    .bg-plan-anual h1, .bg-plan h1{
        font-size: 28pt;
        font-weight: bold;
    }

    .bg-plan{
        background-color: #f2f5f8;
        padding-top: 120px;
        padding-bottom: 120px;
    }

    .border-primary{
        border-width: 3px;
    }
</style>
    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-7">
                <div class="card bg-secondary shadow border-0 card-login-custom">
                    <!-- <div class="card-header bg-transparent pb-5">
                        <div class="text-muted text-center mt-2 mb-3"><small>Registrar-se com</small></div>
                        <div class="btn-wrapper text-center">
                            <a href="#" class="btn btn-neutral btn-icon">
                                <span class="btn-inner--icon"><i class="fab fa-facebook-f"></i></span>
                                <span class="btn-inner--text">Facebook</span>
                            </a>
                            <a href="#" class="btn btn-neutral btn-icon">
                                <span class="btn-inner--icon"><i class="fab fa-google"></i></span>
                                <span class="btn-inner--text">Google</span>
                            </a>
                        </div>
                    </div> -->
                    <div class="card-body px-lg-5 py-lg-5">
                        <!-- <div class="text-center text-muted mb-4">
                            <small>Ou registre-se com seus dados</small>
                        </div> -->
                        <div class="text-muted text-center mb-3"><small class='text-login-bold-white'>{{ trans('supplier.text_register_shop') }}</small></div>

                        <div id="msform">
                            <!-- progressbar -->
                            {{-- <ul id="progressbar">
                                <li class="active" id="account"><strong>{{ trans('supplier.conta') }}</strong></li>
                                <li id="personal"><strong>Planos</strong></li>
                                <li id="payment"><strong>Pagamento</strong></li>
                                <li id="confirm"><strong>Finalizar</strong></li>
                            </ul> <!-- fieldsets --> --}}
                            <fieldset id='page-1-form'>
                                <div class="p-5" style='padding-bottom: 30px !important;'>
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-badge"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="{{ trans('supplier.text_register_shop_name') }}" type="text" name="name" value="{{ old('name') }}"  required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="{{ trans('supplier.text_email') }}" type="email" name="email" value="{{ old('email') ? old('email') : $email }}"  required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-mobile-button"></i></span>
                                            </div>
                                            <input class="form-control phone" placeholder="{{ trans('supplier.text_phone') }}" type="text" name="phone" id='phone'  required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-badge"></i></span>
                                            </div>
                                            <input class="form-control cpf" placeholder="{{ trans('supplier.text_cpf_cnpj') }}" type="text" name="cpf" id='cpf'  required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="{{ trans('supplier.text_password') }}" type="password" name="password"  required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="{{ trans('supplier.text_confirm_password') }}" type="password" name="password_confirmation"  required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="terms_agreed" id="terms-check" required>
                                            <label class="custom-control-label text-login-bold" for="terms-check">{{ trans('supplier.text_terms_of_use01') }} <a href="{{ asset('assets/TermodeUso.pdf') }}" target="_blank">{{ trans('supplier.text_terms_of_use02') }}</a></label>
                                        </div>
                                    </div>
                                </div>
                                <div id='content-form-1-load' class="col-md-12"></div>
                                <button id='submit-form-1' class="btn btn-primary">{{ trans('supplier.button_create_account') }}</button>
                            </fieldset>
                            {{-- <fieldset id='page-2-form'>
                                <div class="p-3">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h3>Cancele quando quiser!</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-2 mb-xl-0 btn mr-0 bg-plan plan-border"  onClick="selectPlanShop(7734, this);">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <div class="form-group text-center">
                                                        <h2 class="mb-3"><br>PLANO MENSAL</h2>
                                                        <h1 class="mb-1">R$ 99,90</h1>                                 
                                                    </div> 
                                                </div><br>
                                                <div class="col-md-12">
                                                    <div class="form-group text-center">
                                                        <h4><i>por mês</i></h4>
                                                    </div>
                                                    <div class="form-group text-center">
                                                        <button class="btn btn-primary">Assine</button>
                                                    </div>
                                                </div>                    
                                            </div>
                                        </div>                            
                                        <div class="col-md-4 mb-2 mb-xl-0 btn mr-0 bg-plan-anual pt-4 pb-4 plan-border border-primary"  onClick="selectPlanShop(7739, this);" >
                                            <div class="form-group">
                                                <div class="col-md-12 mb-12 mb-xl-2">
                                                    <div class="form-group text-center">
                                                        <img class='mb-1' src="{{asset('assets/img/icon plan anual.png')}}" alt="">
                                                        <h2 class="mb-3">PLANO ANUAL</h2>
                                                        <h1 class="mb-0">12x R$ 79,90</h1>
                                                    </div>
                                                </div><br>
                                                <div class="col-md-12 mb-12 mb-xl-0">
                                                    <div class="form-text text-center">
                                                        <h4>R$ 923,70 à vista</h4> 
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-12 mb-xl-0">
                                                    <div class="form-group text-center">
                                                        <button class="btn btn-secondary">Assine Agora</button>
                                                    </div>
                                                </div><br>                    
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2 mb-xl-0 btn mr-0 bg-plan plan-border" onClick="selectPlanShop(7736, this);">
                                            <div class="form-group border-primary">
                                                <div class="col-md-12">
                                                    <div class="form-group text-center">
                                                        <h2 class="mb-3"><br>PLANO SEMESTRAL</h2>
                                                        <h1 class="mb-1">6x R$ 92,87</h1>                                 
                                                    </div> 
                                                </div><br>
                                                <div class="col-md-12">
                                                    <div class="form-group text-center">
                                                        <h4><i>R$ 539,40 à vista</i></h4>
                                                    </div>
                                                    <div class="form-group text-center">
                                                        <button class="btn btn-primary">Assine</button>
                                                    </div>
                                                </div>                    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id='content-form-2-load' class="col-md-12"></div>
                                <button name="previous" class="previous btn btn-secondary" >Anterior</button>
                                <button id='submit-form-2' class="btn btn-primary" >{{ trans('supplier.next') }}</button>
                            </fieldset>
                            <fieldset id='page-3-form'>
                                <div class="p-5">
                                    <h2 class='mb-3'>Informação de Pagamento</h2>
                                    <div class="row">
                                        <div id='content-selected-plan' class="col-md-12 text-left mb-3">
                                            <h3>Plano {{config('app.name')}} para o Lojista Anual</h3>
                                            <h4>R$ 923,70 / Anual</h4>                                            
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="input-group input-group-alternative">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                                                    </div>
                                                    <input class="form-control" placeholder="Nome do Titular" type="text" name="holder" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="input-group input-group-alternative">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-credit-card"></i></span>
                                                    </div>
                                                    <input class="form-control card_number" placeholder="Número do cartão (somente números)" type="text" id='card_number' name="card_number" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="input-group input-group-alternative">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                    </div>
                                                    <input class="form-control" placeholder="Data de expiração MM/AAAA" type="text" id='expiration_date' name="expiration_date" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="input-group input-group-alternative">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                                    </div>
                                                    <input class="form-control" placeholder="Código de segurança (somente números)" type="text" id="security_code" name="security_code" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id='installment-plans'>
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label for="select-installment-plan">Número de Parcelas</label>
                                                <div class="input-group input-group-alternative">                                                    
                                                    <select class="form-control" id="select-installment-plan-annual" name='installment'>
                                                        <option value='1'>1</option>
                                                        <option value='2'>2</option>
                                                        <option value='3'>3</option>
                                                        <option value='4'>4</option>
                                                        <option value='5'>5</option>
                                                        <option value='6'>6</option>
                                                        <option value='7'>7</option>                                                   
                                                        <option value='8'>8</option>
                                                        <option value='9'>9</option>
                                                        <option value='10'>10</option>
                                                        <option value='11'>11</option>
                                                        <option value='12'>12</option>
                                                    </select>
                                                </div>
                                            </div>                                            
                                        </div>
                                        <div class="col-md-3 mt-5">
                                            <div class="form-group mb-3" id='value-plan-annual'>
                                                <h3>1x de <b>R$ 923,70</b></h3>
                                            </div>
                                        </div>                                        
                                    </div>
                                    <div class="row justify-content-end">
                                        <div class="col-md-6 d-flex justify-content-end mt-4">
                                            <div class="row w-100">
                                                <div class="col-md-7">
                                                    <div class="form-group mb-3">
                                                        <div class="input-group input-group-alternative">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="ni ni-money-coins"></i></span>
                                                            </div>
                                                            <input class="form-control" placeholder="Cupom de desconto" type="text" name="coupon" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5 align-items-start">
                                                    <button class='btn btn-danger' id='get-coupon'>Aplicar Cupom</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id='content-form-3-load' class="col-md-12"></div>
                                <button name="previous" class="previous btn btn-secondary">Anterior</button>
                                <button id='submit-form-3' class="btn btn-success">Confirmar</button>
                            </fieldset> --}}
                            <fieldset id='page-4-form'>
                                <div class="p-3 pt-5 pb-5">
                                    <h2 class="text-center">{{ trans('supplier.text_success_register') }}</h2>
                                    <div class="row justify-content-center">
                                        <div class="col-2">
                                            <img src="https://img.icons8.com/color/96/000000/ok--v2.png" class="fit-image">
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div id='finish-register-message' class="col-12 text-center">
                                            <h5>{{ trans('supplier.text_success_register_01') }}</h5>
                                            <p>{{ trans('supplier.text_success_register_email') }} <b></b></p>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">

                    </div>
                </div>
            </div>
        </div>
    </div>    
@stop

@section('scripts')
    <script>
        let emailNewUser = null

        $("#phone").mask('(99) 99999-9999');
        
        $("#cpf").keydown(function(){
            try {
                $("#cpf").unmask();
            } catch (e) {}

            var tamanho = $("#cpf").val().length;

            if(tamanho < 11){
                $("#cpf").mask("999.999.999-99");
            } else {
                $("#cpf").mask("99.999.999/9999-99");
            }

            // ajustando foco
            var elem = this;
            setTimeout(function(){
                // mudo a posição do seletor
                elem.selectionStart = elem.selectionEnd = 10000;
            }, 0);
            // reaplico o valor para mudar o foco
            var currentValue = $(this).val();
            $(this).val('');
            $(this).val(currentValue);
        });

        $(document).ready(function(){ 
            $('#submit-form-1').on('click', function(){
                let name = $("input[name=name]").val()
                let email = $("input[name=email]").val()
                let phone = $("input[name=phone]").val()
                let cpf = $("input[name=cpf]").val()
                let password = $("input[name=password]").val()
                let password_confirmation = $("input[name=password_confirmation]").val()
                let terms_agreed = $("input[name=terms_agreed]:checked").is(':checked') ? 'on' : 'off'

                if(!name){
                    $("input[name=name]").focus()
                    $("#content-form-1-load").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                                                        '<span>O nome da loja é obrigatório</span>'+
                                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                                                            '<span aria-hidden="true">&times;</span>'+
                                                        '</button>'+
                                                    '</div>')
                    return false
                }

                var filterEmail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if (!filterEmail.test(email)) {
                    $("input[name=email]").focus();
                    $("#content-form-1-load").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                                                        '<span>E-mail inválido!</span>'+
                                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                                                            '<span aria-hidden="true">&times;</span>'+
                                                        '</button>'+
                                                    '</div>')
                    return false;
                }

                if(!phone){
                    $("input[name=phone]").focus()
                    $("#content-form-1-load").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                                                        '<span>O telefone é obrigatório</span>'+
                                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                                                            '<span aria-hidden="true">&times;</span>'+
                                                        '</button>'+
                                                    '</div>')
                    return false
                }

                if(!cpf){
                    $("input[name=cpf]").focus()
                    $("#content-form-1-load").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                                                        '<span>O CPF ou CNPJ é obrigatório</span>'+
                                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                                                            '<span aria-hidden="true">&times;</span>'+
                                                        '</button>'+
                                                    '</div>')
                    return false
                }
                
                $.ajax({
                    url: '{{ route("shop.login.post_register.json") }}',
                    method: 'POST',
                    data: { name, email, phone, document: cpf, password, password_confirmation, terms_agreed, _token: "{{ csrf_token() }}" },
                    beforeSend: function(){
                        $("#content-form-1-load").html("<img style='height: 80px;' src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}'>")
                    },
                    success: function(response){
                        $("#page-1-form").hide();
                        $("#page-4-form").show();
                        $('#personal').addClass('active')
                        emailNewUser = email
                    },
                    error: function(response){
                        if(response.responseJSON.message == 'The given data was invalid.'){
                            $("#content-form-1-load").html(
                            "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                                "<span class='alert-text'><strong>Erro</strong> Dados inválidos</span>"+
                                "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                    "<span aria-hidden='true'>&times;</span>"+
                                "</button>"+
                            "</div>");    
                        }else{
                            $("#content-form-1-load").html(
                            "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                                "<span class='alert-text'><strong>Erro</strong> "+response.responseJSON.msg+"</span>"+
                                "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                    "<span aria-hidden='true'>&times;</span>"+
                                "</button>"+
                            "</div>");
                        }
                        
                    }
                });
            })
        })
        
    </script>
@endsection 

{{-- @section('scripts')
    <script>
        let selectedPlan = 7739        
        let number_installments = 1

        let emailNewUser = null

        $("#expiration_date").mask('99/9999');
        $("#phone").mask('(99) 99999-9999');
        $("#card_number").mask("9999 9999 9999 9999");
        $("#security_code").mask("99999");
        
        $("#cpf").keydown(function(){
            try {
                $("#cpf").unmask();
            } catch (e) {}

            var tamanho = $("#cpf").val().length;

            if(tamanho < 11){
                $("#cpf").mask("999.999.999-99");
            } else {
                $("#cpf").mask("99.999.999/9999-99");
            }

            // ajustando foco
            var elem = this;
            setTimeout(function(){
                // mudo a posição do seletor
                elem.selectionStart = elem.selectionEnd = 10000;
            }, 0);
            // reaplico o valor para mudar o foco
            var currentValue = $(this).val();
            $(this).val('');
            $(this).val(currentValue);
        });

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

        function selectPlanShop(planId, e){
            $('.plan-border').removeClass('border-primary')
            $(e).addClass('border-primary')
            selectedPlan = planId
            number_installments = 1

            if(selectedPlan == 7734){
                $('#content-selected-plan').html(
                "<h3>Plano {{config('app.name')}} para o Lojista Mensal</h3>"+
                "<h4>R$ 99,90 / Mensal</h4>");
                $('#installment-plans').html("")
            }

            if(selectedPlan == 7736){
                $('#content-selected-plan').html(
                "<h3>Plano {{config('app.name')}} para o Lojista Semestral</h3>"+
                "<h4>R$ 539,40 / Semestral</h4>");
                $('#installment-plans').html(
                    "<div class='col-md-3'>"+
                        "<div class='form-group mb-3'>"+
                            "<label for='select-installment-plan'>Número de Parcelas</label>"+
                            "<div class='input-group input-group-alternative'>"+                                     
                                "<select class='form-control' id='select-installment-plan-semiannual'>"+
                                    "<option value='1'>1</option>"+
                                    "<option value='2'>2</option>"+
                                    "<option value='3'>3</option>"+
                                    "<option value='4'>4</option>"+
                                    "<option value='5'>5</option>"+
                                    "<option value='6'>6</option>"+                                                    
                                "</select>"+
                            "</div>"+
                        "</div>"+
                    "</div>"+
                    "<div class='col-md-3 mt-5'>"+
                        "<div class='form-group mb-3' id='value-plan-semiannual'>"+
                            "<h3>1x de <b>R$ 539,40</b></h3>"+
                        "</div>"+
                    "</div>")
            }

            if(selectedPlan == 7739){
                $('#content-selected-plan').html(
                "<h3>Plano {{config('app.name')}} para o Lojista Anual</h3>"+
                "<h4>R$ 923,70 / Anual</h4>");

                $('#installment-plans').html(
                    "<div class='col-md-3'>"+
                        "<div class='form-group mb-3'>"+
                            "<label for='select-installment-plan'>Número de Parcelas</label>"+
                            "<div class='input-group input-group-alternative'>"+                                     
                                "<select class='form-control' id='select-installment-plan-annual'>"+
                                    "<option value='1'>1</option>"+
                                    "<option value='2'>2</option>"+
                                    "<option value='3'>3</option>"+
                                    "<option value='4'>4</option>"+
                                    "<option value='5'>5</option>"+
                                    "<option value='6'>6</option>"+
                                    "<option value='7'>7</option>"+                                                  
                                    "<option value='8'>8</option>"+
                                    "<option value='9'>9</option>"+
                                    "<option value='10'>10</option>"+
                                    "<option value='11'>11</option>"+
                                    "<option value='12'>12</option>"+
                                "</select>"+
                            "</div>"+
                        "</div>"+
                    "</div>"+
                    "<div class='col-md-3 mt-5'>"+
                        "<div class='form-group mb-3' id='value-plan-annual'>"+
                            "<h3>1x de <b>R$ 923,70</b></h3>"+
                        "</div>"+
                    "</div>")
            }
        }

        $(document).ready(function(){ 
            $('#get-coupon').on('click', function(){
                let coupon = $("input[name=coupon]").val()
                $.ajax({
                    url: '{{ route("shop.coupon.subscription.isvalid") }}',
                    method: 'GET',
                    data: { coupon, selectedPlan },
                    beforeSend: function(){
                        //$("#installment-plans").html("<img style='height: 80px;' src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}'>")
                    },
                    success: function(response){
                        $('#content-selected-plan h4').html(response.content_selected_plan_text)
                        installmentAnnual = response.installmentsDiscount                        
                        $('#value-plan-annual').html(response.value)
                        $('#select-installment-plan-annual option:eq(0)').prop('selected', true)
                        //$("#installment-plans").html(response)
                    },
                    error: function(response){
                        
                    }
                });
            })

            $('#submit-form-1').on('click', function(){
                let name = $("input[name=name]").val()
                let email = $("input[name=email]").val()
                let phone = $("input[name=phone]").val()
                let cpf = $("input[name=cpf]").val()
                let password = $("input[name=password]").val()
                let password_confirmation = $("input[name=password_confirmation]").val()
                let terms_agreed = $("input[name=terms_agreed]:checked").is(':checked') ? 'on' : 'off'

                if(!name){
                    $("input[name=name]").focus()
                    $("#content-form-1-load").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                                                        '<span>O nome da loja é obrigatório</span>'+
                                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                                                            '<span aria-hidden="true">&times;</span>'+
                                                        '</button>'+
                                                    '</div>')
                    return false
                }

                var filterEmail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if (!filterEmail.test(email)) {
                    $("input[name=email]").focus();
                    $("#content-form-1-load").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                                                        '<span>E-mail inválido!</span>'+
                                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                                                            '<span aria-hidden="true">&times;</span>'+
                                                        '</button>'+
                                                    '</div>')
                    return false;
                }

                if(!phone){
                    $("input[name=phone]").focus()
                    $("#content-form-1-load").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                                                        '<span>O telefone é obrigatório</span>'+
                                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                                                            '<span aria-hidden="true">&times;</span>'+
                                                        '</button>'+
                                                    '</div>')
                    return false
                }

                if(!cpf){
                    $("input[name=cpf]").focus()
                    $("#content-form-1-load").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                                                        '<span>O CPF ou CNPJ é obrigatório</span>'+
                                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                                                            '<span aria-hidden="true">&times;</span>'+
                                                        '</button>'+
                                                    '</div>')
                    return false
                }
                
                $.ajax({
                    url: '{{ route("shop.login.post_register.json") }}',
                    method: 'POST',
                    data: { name, email, phone, document: cpf, password, password_confirmation, terms_agreed, _token: "{{ csrf_token() }}" },
                    beforeSend: function(){
                        $("#content-form-1-load").html("<img style='height: 80px;' src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}'>")
                    },
                    success: function(response){
                        $("#page-1-form").hide();
                        $("#page-2-form").show();
                        $('#personal').addClass('active')
                        emailNewUser = email
                    },
                    error: function(response){
                        if(response.responseJSON.message == 'The given data was invalid.'){
                            $("#content-form-1-load").html(
                            "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                                "<span class='alert-text'><strong>Erro</strong> Dados inválidos</span>"+
                                "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                    "<span aria-hidden='true'>&times;</span>"+
                                "</button>"+
                            "</div>");    
                        }else{
                            $("#content-form-1-load").html(
                            "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                                "<span class='alert-text'><strong>Erro</strong> "+response.responseJSON.msg+"</span>"+
                                "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                    "<span aria-hidden='true'>&times;</span>"+
                                "</button>"+
                            "</div>");
                        }
                        
                    }
                });
            })

            $('#submit-form-2').on('click', function(){
                $.ajax({
                    url: '{{ route("shop.login.post_register.plan.json") }}',
                    method: 'POST',
                    data: { _token: "{{ csrf_token() }}", plan_id: selectedPlan },
                    beforeSend: function(){
                        $("#content-form-2-load").html("<img style='height: 80px;' src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}'>")
                    },
                    success: function(response){
                        $("#page-2-form").hide();
                        $("#page-3-form").show();
                        $('#payment').addClass('active')
                    },
                    error: function(response){
                        
                        $("#content-form-2-load").html(
                            "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                                "<span class='alert-text'><strong>Erro</strong> "+response.responseJSON.msg+"</span>"+
                                "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                    "<span aria-hidden='true'>&times;</span>"+
                                "</button>"+
                            "</div>");
                    }
                });
            })

            $('#submit-form-3').on('click', function(){
                let holder = $("input[name=holder]").val()
                let card_number = $("input[name=card_number]").val()
                let expiration_date = $("input[name=expiration_date]").val()
                let security_code = $("input[name=security_code]").val()
                let coupon = $("input[name=coupon]").val()

                if(expiration_date.length < 7){
                    $("#content-form-3-load").html(
                            "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                                "<span class='alert-text'><strong>Erro</strong> A data de expiração tem que ser no formato MM/AAAA, ex: 04/2024</span>"+
                                "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                    "<span aria-hidden='true'>&times;</span>"+
                                "</button>"+
                            "</div>");
                    return null
                }

                $.ajax({
                    url: '{{ route("shop.login.post_register.card.json") }}',
                    method: 'POST',
                    data: { _token: "{{ csrf_token() }}", holder, card_number, expiration_date, security_code, number_installments, coupon },
                    beforeSend: function(){
                        $("#content-form-3-load").html("<img style='height: 80px;' src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}'>")
                    },
                    success: function(response){
                        $("#page-3-form").hide();
                        $('#finish-register-message').html(
                            "<h5>{{ trans('supplier.text_success_register_01') }}</h5>"+
                            "<p>{{ trans('supplier.text_success_register_email') }} <b>"+emailNewUser+"</b></p>"
                        );
                        $('#confirm').addClass('active')
                        $("#page-4-form").show();
                    },
                    error: function(response){
                        $("#content-form-3-load").html(
                            "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                                "<span class='alert-text'><strong>Erro</strong> "+response.responseJSON.msg+"</span>"+
                                "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                    "<span aria-hidden='true'>&times;</span>"+
                                "</button>"+
                            "</div>");
                    }
                });
            })
        })
        
    </script>
@endsection   --}}

