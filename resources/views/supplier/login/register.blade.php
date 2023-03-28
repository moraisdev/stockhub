@extends('supplier.login.layout')

@section('content')
    <!-- Page content -->
    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
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
                        <div class="text-muted text-center mb-3"><small class='text-login-bold-white'>Cadastrar-se como Fornecedor</small></div>

                        <form role="form" method="POST" action="{{ route('supplier.login.post_register') }}">
                            {{ csrf_field() }}
                            <div class="form-group mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-badge"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="Nome do Fornecedor" type="text" name="name" value="{{ old('name') }}" readonly required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="E-mail" type="email" name="email" value="{{ old('email') ? old('email') : $email }}" readonly required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="Senha" type="password" name="password" readonly required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="Confirmar Senha" type="password" name="password_confirmation" readonly required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="terms_agreed" id="terms-check" required>
                                    <label class="custom-control-label text-login-bold-white" for="terms-check">Eu li e concordo com os <a href="{{ asset('assets/TermodeUso.pdf') }}" target="_blank">Termos e Condições de uso</a> do {{config('app.name')}}.</label>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary my-4 btn-login-custom">Cadastrar-se</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">

                    </div>
                    <div class="col-6 text-right">
                        <a href="{{ route('supplier.login') }}" class="text-light"><small class='text-login-bold-white'>Login</small></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
