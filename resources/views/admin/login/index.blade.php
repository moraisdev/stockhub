@extends('admin.login.layout')

@section('content')
    <!-- Page content -->
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
                        <div class="text-muted text-center mb-3"><small class='text-login-bold-white'>{{ trans('supplier.text_login') }}</small></div>

                        <form role="form" method="POST" action="{{ route('admin.login.authenticate') }}">
                            {{ csrf_field() }}
                            <div class="form-group mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="{{ trans('supplier.text_email') }}" type="email" name="email" value="{{ old('email') }}" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="{{ trans('supplier.text_password') }}" type="password" name="password" readonly>
                                </div>
                            </div>
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input class="custom-control-input" id=" customCheckLogin" type="checkbox" value="1" name="keep_user_connected" {{ (old('keep_user_connected') == 1) ? 'checked' : '' }}>
                                <label class="custom-control-label" for=" customCheckLogin">
                                    <span class="text-muted text-login-bold-white">{{ trans('supplier.text_check_connection') }}</span>
                                </label>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary my-4 btn-login-custom">{{ trans('supplier.text_login') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop