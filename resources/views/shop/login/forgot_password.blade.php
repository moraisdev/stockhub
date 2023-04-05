@extends('shop.login.layout')

@section('content')
    <!-- Page content -->
    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card bg-secondary shadow border-0 card-login-custom">
                    <div class="card-body px-lg-5 py-lg-5">
                        <div class="text-muted text-center mb-3"><small class='text-login-bold-white'>{{ trans('supplier.text_password_recovery') }}</small></div>

                        <form role="form" method="POST" action="{{ route('shop.login.forgot_password.post') }}">
                            {{ csrf_field() }}
                            <div class="form-group mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="{{ trans('supplier.text_password_recovery_email') }}" type="email" name="email" value="{{ old('email') }}" readonly>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary my-4 btn-login-custom">{{ trans('supplier.text_password_recovery_button') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">
                        <a href="{{ route('shop.login') }}" class="text-light"><small class='text-login-bold-white'>{{ trans('supplier.text_login') }}</small></a> / 
                    </div>
                    <div class="col-6 text-right">
                        <a href="{{ route('shop.login.register') }}" class="text-light"><small class='text-login-bold-white'>{{ trans('supplier.button_create_account_nav') }}</small></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop