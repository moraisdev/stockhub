@extends('shop.login.layout')
@section('content')
<link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>

@section('content')
    <style>
        html {
        background-color: #fff;
        }
        body {
            font-family: "Poppins", sans-serif;
            height: 100vh;
            }

        a {
            color: #92badd;
            display:inline-block;
            text-decoration: none;
            font-weight: 400;
            }

        h2 {
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            display:inline-block;
            margin: 40px 8px 10px 8px; 
            color: #cccccc;
            }

        .wrapper {
            display: flex;
            align-items: center;
            flex-direction: column; 
            justify-content: center;
            width: 100%;
            min-height: 100%;
            padding: 20px;
            }

        #formContent {
            -webkit-border-radius: 10px 10px 10px 10px;
            border-radius: 10px 10px 10px 10px;
            background: #fff;
            padding: 30px;
            width: 90%;
            max-width: 360px;
            position: relative;
            -webkit-box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
            box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
            text-align: center;
            padding-bottom: 30px;
        }
        #formFooter {
            display: flex;
            justify-content: space-between;
        }

        .checkbox-container {
            align-items: center;
        }

        .link-container {
                display: flex;
                justify-content: space-between;
                width: 100%;
                margin-top: 20px;
                padding: 0 10px;
            }

        .link-container a, .checkbox-container label {
            margin: 0 10px;
            white-space: nowrap;
            font-size: 14px;
            margin-top: 20px;
        }

        h2.inactive {
            color: #cccccc;
            }

        h2.active {
            color: #0d0d0d;
            border-bottom: 2px solid #02A0FC;
            }

        input[type=button], input[type=submit], input[type=reset]  {
            
            background-color: #02A0FC;
            border: none;
            color: white;
            padding: 15px 80px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            text-transform: uppercase;
            font-size: 13px;
            margin-right: 15%;
            -webkit-box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
            box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
            -webkit-border-radius: 5px 5px 5px 5px;
            border-radius: 5px 5px 5px 5px;
            -webkit-transition: all 0.3s ease-in-out;
            -moz-transition: all 0.3s ease-in-out;
            -ms-transition: all 0.3s ease-in-out;
            -o-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }
        
        input[type=submit] {
            width: 93%;
            max-width: 100%;
            padding: 12px;
        }
        input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover  {
            background-color: #39ace7;
            }

        input[type=button]:active, input[type=submit]:active, input[type=reset]:active  {
            -moz-transform: scale(0.95);
            -webkit-transform: scale(0.95);
            -o-transform: scale(0.95);
            -ms-transform: scale(0.95);
            transform: scale(0.95);
            }

        input[type=text], input[type=password], input[type=email], input[type=tel] {
            background-color: #fff;
            border: none;
            color: #0d0d0d;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 13px;
            margin: 5px;
            width: 85%;
            border: 2px solid #f6f6f6;
            -webkit-transition: all 0.5s ease-in-out;
            -moz-transition: all 0.5s ease-in-out;
            -ms-transition: all 0.5s ease-in-out;
            -o-transition: all 0.5s ease-in-out;
            transition: all 0.5s ease-in-out;
            -webkit-border-radius: 5px 5px 5px 5px;
            border-radius: 5px 5px 5px 5px;
        }

        input[type=text]:focus, input[type=password]:focus, input[type=email]:focus, input[type=tel]:focus {
            background-color: #fff;
            border-bottom: 2px solid #02A0FC;
        }

        input[type=text]:focus {
            background-color: #fff;
            border-bottom: 2px solid #02A0FC;
            }

        input[type=text]:placeholder {
            color: #cccccc;

        }
        
        .terms-text label {
                font-size: 12px; 
                margin-right: 10%;
            }

        .fadeInDown {
            -webkit-animation-name: fadeInDown;
            animation-name: fadeInDown;
            -webkit-animation-duration: 1s;
            animation-duration: 1s;
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both;
            }

        @-webkit-keyframes fadeInDown {
            0% {
                opacity: 0;
                -webkit-transform: translate3d(0, -100%, 0);
                transform: translate3d(0, -100%, 0);
            }
            100% {
                opacity: 1;
                -webkit-transform: none;
                transform: none;
            }
            }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                -webkit-transform: translate3d(0, -100%, 0);
                transform: translate3d(0, -100%, 0);
            }
            100% {
                opacity: 1;
                -webkit-transform: none;
                transform: none;
            }
            }

        @-webkit-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @-moz-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        .fadeIn {
            
            opacity:0;
            -webkit-animation:fadeIn ease-in 1;
            -moz-animation:fadeIn ease-in 1;
            animation:fadeIn ease-in 1;

            -webkit-animation-fill-mode:forwards;
            -moz-animation-fill-mode:forwards;
            animation-fill-mode:forwards;

            -webkit-animation-duration:1s;
            -moz-animation-duration:1s;
            animation-duration:1s;
            }

        .fadeIn.first {
            -webkit-animation-delay: 0.4s;
            -moz-animation-delay: 0.4s;
            animation-delay: 0.4s;
            }

        .fadeIn.second {
            -webkit-animation-delay: 0.6s;
            -moz-animation-delay: 0.6s;
            animation-delay: 0.6s;
            }

        .fadeIn.third {
            -webkit-animation-delay: 0.8s;
            -moz-animation-delay: 0.8s;
            animation-delay: 0.8s;
            }

        .fadeIn.fourth {
            -webkit-animation-delay: 1s;
            -moz-animation-delay: 1s;
            animation-delay: 1s;
            }
      
            .underlineHover:after {
            display: block;
            left: 0;
            bottom: -10px;
            width: 0;
            height: 2px;
            background-color: #56baed;
            content: "";
            transition: width 0.2s;
            }

            .underlineHover:hover {
            color: #0d0d0d;
            }

            .underlineHover:hover:after{
            width: 100%;
            }

            *:focus {
                outline: none;
            } 

            #icon {
            width:60%;
            }

            * {
            box-sizing: border-box;
            }

        .cookieConsentContainer {
            z-index: 999;
            width: 350px;
            min-height: 20px;
            box-sizing: border-box;
            padding: 30px 30px 30px 30px;
            background: #232323;
            overflow: hidden;
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: none
        }
        #formContent h2.active,
        #formContent h2.inactive {
            font-size: 14px;
        }
        .cookieConsentContainer .cookieTitle a {
            font-family: OpenSans, arial, sans-serif;
            color: #fff;
            font-size: 22px;
            line-height: 20px;
            display: block
        }

        .cookieConsentContainer .cookieDesc p {
            margin: 0;
            padding: 0;
            font-family: OpenSans, arial, sans-serif;
            color: #fff;
            font-size: 13px;
            line-height: 20px;
            display: block;
            margin-top: 10px
        }

        .cookieConsentContainer .cookieDesc a {
            font-family: OpenSans, arial, sans-serif;
            color: #fff;
            text-decoration: underline
        }

        .cookieConsentContainer .cookieButton a {
            display: inline-block;
            font-family: OpenSans, arial, sans-serif;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            margin-top: 14px;
            background: #000;
            box-sizing: border-box;
            padding: 15px 24px;
            text-align: center;
            transition: background .3s
        }

        .cookieConsentContainer .cookieButton a:hover {
            cursor: pointer;
            background: #3e9b67
        }

        @media (max-width:980px) {
            .cookieConsentContainer {
                bottom: 0 !important;
                left: 0 !important;
                width: 100% !important
            }
        }
</style>

            <div class="wrapper fadeInDown">
                <div id="formContent">

                    <div class="fadeIn first">
                        <img src="{{ asset('assets/img/brand/logo.png?v=2') }}" id="icon" alt="User Icon" />
                    </div>

                    <h2 class="inactive underlineHover"><a href="{{ route('shop.login') }}" style="color: inherit;">Login</a></h2>
                    <h2 class="active">Nova Conta</h2>

                    <form method="POST" action="{{ route('shop.login.register') }}">
                    {{ csrf_field() }}
                                <input type="text" id="shop-name" class="fadeIn second" name="name" placeholder="Nome e sobrenome" required>
                                <input type="email" id="email" class="fadeIn third" name="email" placeholder="E-mail" required>
                                <input type="tel" id="phone" class="fadeIn third" name="phone" placeholder="Telefone" required>
                                <input type="password" id="password" class="fadeIn third" name="password" placeholder="Senha" required>
                                <input type="password" id="password-confirm" class="fadeIn third" name="password_confirmation" placeholder="Repetir senha" required>
                                <div class="custom-control custom-checkbox fadeIn fourth">
                                <div class="terms-text" id="terms-check" name="terms_agreed" required>
    <label for="terms-check">Ao criar conta, você confirma que leu e concorda com os <a href="{{ asset('assets/TermodeUso.pdf') }}" target="_blank">termos de uso</a> da Stockhub.</label>
</div>

                                <input type="submit" class="fadeIn fourth" value="Criar conta grátis">

                            <fieldset id='page-4-form' style="display: none;">
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
    </div>    
@endsection

@section('scripts')
    <script>
        let emailNewUser = null

        $("#phone").mask('(99) 99999-9999');
        
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
                }

                
                $.ajax({
                    url: '{{ route("shop.login.post_register.json") }}',
                    method: 'POST',
                    data: { name, email, phone, document: null, password, password_confirmation, terms_agreed, _token: "{{ csrf_token() }}" },
                    beforeSend: function(){
                        $("#content-form-1-load").html("<img style='height: 80px;' src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}'>")
                    },
                    success: function(response){
                        console.log("AJAX Success Response:", response);
                        
                        $("#page-1-form").hide();
                        $("#page-4-form").css('display', 'block');
                        $('#personal').addClass('active')
                        emailNewUser = email
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error: ", status, error);
                        console.log("Response Text: ", xhr.responseText);

                        var errorMsg = "An unknown error occurred";
                        if (xhr.responseJSON && xhr.responseJSON.msg) {
                            errorMsg = xhr.responseJSON.msg;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        $("#content-form-1-load").html(
                            "<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                                "<span class='alert-text'><strong>Error:</strong> " + errorMsg + "</span>" +
                                "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                                    "<span aria-hidden='true'>&times;</span>" +
                                "</button>" +
                            "</div>"
                    );
}
                });
            })

            
        
    </script>
@endsection 