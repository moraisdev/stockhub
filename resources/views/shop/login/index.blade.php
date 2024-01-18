@extends('shop.login.layout')

@section('content')
    <style>
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
    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card bg-secondary shadow border-0 card-login-custom">
                    <!-- <div class="card-header bg-transparent pb-5">
                                        <div class="text-muted text-center mt-2 mb-3"><small>Entrar com</small></div>
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
                                            <small>Ou entre com suas credenciais</small>
                                        </div> -->
                        <div class="text-muted text-center mb-3"><small class='text-login-bold-white'>Login</small></div>

                        <form role="form" method="POST" action="{{ route('shop.login.authenticate') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="redirect_url" value="{{ $redirect_url }}">
                            <div class="form-group mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="Email" type="email" name="email"
                                        value="{{ old('email') }}" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="Senha" type="password" name="password"
                                        readonly>
                                </div>
                            </div>
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input class="custom-control-input" id=" customCheckLogin" type="checkbox" value="1"
                                    name="keep_user_connected" {{ old('keep_user_connected') == 1 ? 'checked' : '' }}>
                                <label class="custom-control-label" for=" customCheckLogin">
                                    <span class="text-muted text-login-bold-white">Manter-me conectado</span>
                                </label>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary my-4 btn-login-custom">Entrar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">
                        <a href="{{ route('shop.login.forgot_password') }}" class="text-light"><small
                                class='text-login-bold-white'>Esqueci minha senha</small></a>
                    </div>
                    <div class="col-6 text-right">
                        <a href="{{ route('shop.login.register') }}" class="text-light"><small
                                class='text-login-bold-white'>Criar nova conta</small></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
