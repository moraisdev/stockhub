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
            -webkit-box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
            box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
            -webkit-border-radius: 5px 5px 5px 5px;
            border-radius: 5px 5px 5px 5px;
            margin: 5px 20px 40px 20px;
            -webkit-transition: all 0.3s ease-in-out;
            -moz-transition: all 0.3s ease-in-out;
            -ms-transition: all 0.3s ease-in-out;
            -o-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
            }
        
        input[type=submit] {
            width: 85%;
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

        input[type=text], input[type=password] {
            background-color: #f6f6f6;
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

        input[type=text]:focus, input[type=password]:focus {
            background-color: #fff;
            border-bottom: 2px solid #02A0FC;
            }

        input[type=text]:placeholder {
            color: #cccccc;
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
        #customCheckLogin + label {
            cursor: pointer;
        }
        label[for="customCheckLogin"] {
            margin-right: 5px;
            transform: scale(0.9);

        }
        label[for="customCheckLogin"], .link-container a {
            font-size: 14px;
            background
        }
        input[type=checkbox] {
            transform: scale(0.1); /* Reduz um pouco o tamanho do checkbox */
            margin-right: 5px;
        }
            /* Simple CSS3 Fade-in Animation */
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

            <h2 class="active">Login</h2>
            <h2 class="inactive underlineHover"><a href="{{ route('shop.login.register') }}" style="color: inherit;">Nova Conta</a></h2>

            <form method="POST" action="{{ route('shop.login.authenticate') }}">
            {{ csrf_field() }}
            <input type="hidden" name="redirect_url" value="{{ $redirect_url }}">
            <input type="text" id="login" class="fadeIn second" name="email" placeholder="E-mail" value="{{ old('email') }}">
            <input type="password" id="password" class="fadeIn third" name="password" placeholder="Senha">
            <input type="submit" class="fadeIn fourth" value="Acessar minha conta">

            <div class="link-container">
            <div class="checkbox-container">
                    <input class="custom-control-input" id="customCheckLogin" type="checkbox" value="1" name="keep_user_connected" {{ old('keep_user_connected') == 1 ? 'checked' : '' }}>
                    <label class="custom-control-label" for="customCheckLogin">Lembrar-me</label>
                </div>
                <a class="underlineHover" href="{{ route('shop.login.forgot_password') }}">Esqueci a senha</a>
            </div>
        </form>
        </div>
    </div>
    @endsection


    <script>
        var purecookieTitle = "Cookies.",
            purecookieDesc = "Ao usar esse site, automaticamente você aceita o uso de cookies.",
            purecookieLink = '<a href="https://www.cssscript.com/privacy-policy/" target="_blank">Saiba mais</a>',
            purecookieButton = "Entendi";

        function pureFadeIn(e, o) {
            var i = document.getElementById(e);
            i.style.opacity = 0, i.style.display = o || "block",
                function e() {
                    var o = parseFloat(i.style.opacity);
                    (o += .02) > 1 || (i.style.opacity = o, requestAnimationFrame(e))
                }()
        }

        function pureFadeOut(e) {
            var o = document.getElementById(e);
            o.style.opacity = 1,
                function e() {
                    (o.style.opacity -= .02) < 0 ? o.style.display = "none" : requestAnimationFrame(e)
                }()
        }

        function setCookie(e, o, i) {
            var t = "";
            if (i) {
                var n = new Date;
                n.setTime(n.getTime() + 24 * i * 60 * 60 * 1e3), t = "; expires=" + n.toUTCString()
            }
            document.cookie = e + "=" + (o || "") + t + "; path=/"
        }

        function getCookie(e) {
            for (var o = e + "=", i = document.cookie.split(";"), t = 0; t < i.length; t++) {
                for (var n = i[t];
                    " " == n.charAt(0);) n = n.substring(1, n.length);
                if (0 == n.indexOf(o)) return n.substring(o.length, n.length)
            }
            return null
        }

        function eraseCookie(e) {
            document.cookie = e + "=; Max-Age=-99999999;"
        }

        function cookieConsent() {
            getCookie("purecookieDismiss") || (document.body.innerHTML +=
                '<div class="cookieConsentContainer" id="cookieConsentContainer"><div class="cookieTitle"><a>' +
                purecookieTitle + '</a></div><div class="cookieDesc"><p>' + purecookieDesc + " " + purecookieLink +
                '</p></div><div class="cookieButton"><a onClick="purecookieDismiss();">' + purecookieButton +
                "</a></div></div>", pureFadeIn("cookieConsentContainer"))
        }

        function purecookieDismiss() {
            setCookie("purecookieDismiss", "1", 7), pureFadeOut("cookieConsentContainer")
        }
        window.onload = function() {
            cookieConsent()
        };

    </script>
@stop
