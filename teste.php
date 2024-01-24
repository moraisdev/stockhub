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
                        <div class="text-muted text-center mb-3"><small class='text-login-bold-white'>Cadastrar-se como lojista</small></div>

                        <div id="msform">
                            <!-- progressbar -->
                            {{-- <ul id="progressbar">
                                <li class="active" id="account"><strong>Conta</strong></li>
                                <li id="confirm"><strong>Finalizar</strong></li>
                            </ul> <!-- fieldsets --> --}}
                            <fieldset id='page-1-form'>
                                <div class="p-5" style='padding-bottom: 30px !important;'>
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-badge"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="Nome da sua loja" type="text" name="name" value="{{ old('name') }}"  required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="Email" type="email" name="email" value="{{ old('email') ? old('email') : $email }}"  required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-mobile-button"></i></span>
                                            </div>
                                            <input class="form-control phone" placeholder="Telefone" type="text" name="phone" id='phone'  required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-badge"></i></span>
                                            </div>
                                            <input class="form-control cpf" placeholder="CPF ou CNPJ" type="text" name="cpf" id='cpf'  required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="Senha" type="password" name="password"  required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="Confirmar senha" type="password" name="password_confirmation"  required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="terms_agreed" id="terms-check" required>
                                            <label class="custom-control-label text-login-bold" for="terms-check">Eu li e concordo com os <a href="{{ asset('assets/TermodeUso.pdf') }}" target="_blank">Termos e Condições de uso</a> do {{config('app.name')}}.</label>
                                        </div>
                                    </div>
                                </div>
                                <div id='content-form-1-load' class="col-md-12"></div>
                                <button id='submit-form-1' class="btn btn-primary">Próximo</button>
                            </fieldset>

                            <fieldset id='page-4-form'>
                                <div class="p-3 pt-5 pb-5">
                                    <h2 class="text-center">Sucesso!</h2>
                                    <div class="row justify-content-center">
                                        <div class="col-2">
                                            <img src="https://img.icons8.com/color/96/000000/ok--v2.png" class="fit-image">
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div id='finish-register-message' class="col-12 text-center">
                                            <h5>Você concluiu seu registro!</h5>
                                            <p>Foi enviado um e-mail de verificação para o e-mail <b></b></p>
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
                    <div class="col-6 text-right">
                        <a href="{{ route('shop.login') }}" class="text-light"><small class='text-login-bold-white'>Login</small></a>
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
                        // }else if(response.responseJSON.msg == 'undefined'){
                           
                        //     $("#content-form-1-load").html(
                        //     "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                        //         "<span class='alert-text'><strong>Erro</strong> indefinido. Possivelmente Route.</span>"+
                        //         "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                        //             "<span aria-hidden='true'>&times;</span>"+
                        //         "</button>"+
                        //     "</div>"); 
                            
                        // }else{
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
                        $("#content-form-1-load").html(
                            "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                                "<span class='alert-text'><strong>Error</strong> Teste</span>"+
                                "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                    "<span aria-hidden='true'>&times;</span>"+
                                "</button>"+
                            "</div>");
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
                    //     }else if(response.responseJSON.msg == 'undefined'){
                           
                    //        $("#content-form-1-load").html(
                    //        "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                    //            "<span class='alert-text'><strong>Erro</strong> indefinido. Possivelmente Route2.</span>"+
                    //            "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                    //                "<span aria-hidden='true'>&times;</span>"+
                    //            "</button>"+
                    //        "</div>"); 
                           
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

            })
        })
        
    </script>
@endsection   --}}
