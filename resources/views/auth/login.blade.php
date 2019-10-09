@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>
                <div class="card-body">
                    <form id="login-form" method="POST" action="{{ route('authenticate') }}">
                        @csrf

                        @if ($errors->has('token'))
                            <h3>
                                <strong>{{ $errors->first('token') }}</strong>
                            </h3>
                        @endif

                        <!-- Выбор логиниться через почту и статичный пароль или через временный пароль -->
                        <div class="form-check form-group row">
                            <input class="form-check-input" type="radio" name="login_type" id="login_mail" value="permanent" checked>
                            <label class="form-check-label" for="login_mail">
                              Login by permanent password
                            </label>
                        </div>
                        <div class="form-check form-group row">
                            <input class="form-check-input" type="radio" name="login_type" id="login_phone" value="dynamic">
                            <label class="form-check-label" for="login_phone">
                                Login by dynamic password
                            </label>
                        </div>

                        <!-- Выбор логиниться через временный пароль на почту или на телефон -->
                        <div class="form-check form-group row login_form login-dynamic" style="display:none;">
                            <input class="form-check-input" type="radio" name="dynamic_type" id="dynamic_phone" value="phone" checked>
                            <label class="form-check-label" for="dynamic_phone">
                              Login by phone
                            </label>
                        </div>
                        <div class="form-check form-group row login_form login-dynamic" style="display:none;">
                            <input class="form-check-input" type="radio" name="dynamic_type" id="dynamic_mail" value="email">
                            <label class="form-check-label" for="dynamic_mail">
                                Login by mail
                            </label>
                        </div>

                        
                            <div class="form-group row dynamic dynamic-email login-permanent login_form">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
    
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" autofocus>
                                    
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                    <span class="dynamic_errors" id="dynamic_email_error" style="display:none;">@lang('auth.check_email')</span>
                                </div>
                            </div>
    
                            <div class="form-group row login-permanent login_form">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
    
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">
    
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-0 login-permanent login_form">
                                <div class="col-md-8 offset-md-4">
                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row dynamic dynamic-phone login_form login-dynamic" style="display:none;">
                                <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>
    
                                <div class="col-md-6">
                                    <input id="phone" type="phone" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" >
                                    @if ($errors->has('phone'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif
                                    <span class="dynamic_errors" id="dynamic_phone_error" style="display:none;">@lang('auth.check_phone')</span>
                                </div>
                            </div>
                            <div class="form-group row  login_form login-dynamic" style="display:none;">
                                <span class="btn btn-link" id="get_password" href="#">
                                    {{ __('Get password') }}
                                </span>
                            </div>

                            <div class="form-group row temp_password login_form login-dynamic" style="display:none;">
                                <label for="token" class="col-md-4 col-form-label text-md-right">{{ __('Temporary Password') }}</label>
    
                                <div class="col-md-6 ">
                                    <input id="token" type="number" class="form-control{{ $errors->has('token') ? ' is-invalid' : '' }}" name="token">
                                </div>
                            </div>

                        <div class="form-group row mb-0 login-btn">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
