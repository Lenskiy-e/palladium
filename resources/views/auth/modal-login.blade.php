{{--<div class="mod-login">--}}
{{--<div class="card">--}}
{{--<div class="card-header">{{ __('Login') }}</div>--}}
{{--<div class="card-body">--}}
{{--<form id="login-form">--}}
{{--@csrf--}}

{{--@if ($errors->has('token'))--}}
{{--<h3>--}}
{{--<strong>{{ $errors->first('token') }}</strong>--}}
{{--</h3>--}}
{{--@endif--}}

{{--<!-- Выбор логиниться через почту и статичный пароль или через временный пароль -->--}}
{{--<div class="form-check form-group row">--}}
{{--<input class="form-check-input" type="radio" name="login_type" id="login_mail" value="permanent"--}}
{{--checked>--}}
{{--<label class="form-check-label" for="login_mail">--}}
{{--Login by permanent password--}}
{{--</label>--}}
{{--</div>--}}
{{--<div class="form-check form-group row">--}}
{{--<input class="form-check-input" type="radio" name="login_type" id="login_phone" value="dynamic">--}}
{{--<label class="form-check-label" for="login_phone">--}}
{{--Login by dynamic password--}}
{{--</label>--}}
{{--</div>--}}

{{--<!-- Выбор логиниться через временный пароль на почту или на телефон -->--}}
{{--<div class="form-check form-group row login_form login-dynamic" style="display:none;">--}}
{{--<input class="form-check-input" type="radio" name="dynamic_type" id="dynamic_phone" value="phone"--}}
{{--checked>--}}
{{--<label class="form-check-label" for="dynamic_phone">--}}
{{--Login by phone--}}
{{--</label>--}}
{{--</div>--}}
{{--<div class="form-check form-group row login_form login-dynamic" style="display:none;">--}}
{{--<input class="form-check-input" type="radio" name="dynamic_type" id="dynamic_mail" value="email">--}}
{{--<label class="form-check-label" for="dynamic_mail">--}}
{{--Login by mail--}}
{{--</label>--}}
{{--</div>--}}


{{--<div class="form-group row dynamic dynamic-email login-permanent login_form">--}}
{{--<label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>--}}

{{--<div class="col-md-6">--}}
{{--<input id="email" type="email"--}}
{{--class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"--}}
{{--value="{{ old('email') }}" autofocus>--}}

{{--@if ($errors->has('email'))--}}
{{--<span class="invalid-feedback" role="alert">--}}
{{--<strong>{{ $errors->first('email') }}</strong>--}}
{{--</span>--}}
{{--@endif--}}
{{--<span class="dynamic_errors" id="dynamic_email_error"--}}
{{--style="display:none;">@lang('auth.check_email')</span>--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="form-group row login-permanent login_form">--}}
{{--<label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>--}}

{{--<div class="col-md-6">--}}
{{--<input id="password" type="password"--}}
{{--class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">--}}

{{--@if ($errors->has('password'))--}}
{{--<span class="invalid-feedback" role="alert">--}}
{{--<strong>{{ $errors->first('password') }}</strong>--}}
{{--</span>--}}
{{--@endif--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="form-group row mb-0 login-permanent login_form">--}}
{{--<div class="col-md-8 offset-md-4">--}}
{{--@if (Route::has('password.request'))--}}
{{--<a class="btn btn-link" href="{{ route('password.request') }}">--}}
{{--{{ __('Forgot Your Password?') }}--}}
{{--</a>--}}
{{--@endif--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="form-group row dynamic dynamic-phone login_form login-dynamic" style="display:none;">--}}
{{--<label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>--}}

{{--<div class="col-md-6">--}}
{{--<input id="phone" type="phone"--}}
{{--class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone"--}}
{{--value="{{ old('phone') }}">--}}
{{--@if ($errors->has('phone'))--}}
{{--<span class="invalid-feedback" role="alert">--}}
{{--<strong>{{ $errors->first('phone') }}</strong>--}}
{{--</span>--}}
{{--@endif--}}
{{--<span class="dynamic_errors" id="dynamic_phone_error"--}}
{{--style="display:none;">@lang('auth.check_phone')</span>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="form-group row  login_form login-dynamic" style="display:none;">--}}
{{--<span class="btn btn-link" id="get_password" href="#">--}}
{{--{{ __('Get password') }}--}}
{{--</span>--}}
{{--</div>--}}

{{--<div class="form-group row temp_password login_form login-dynamic" style="display:none;">--}}
{{--<label for="token"--}}
{{--class="col-md-4 col-form-label text-md-right">{{ __('Temporary Password') }}</label>--}}

{{--<div class="col-md-6 ">--}}
{{--<input id="token" type="number"--}}
{{--class="form-control{{ $errors->has('token') ? ' is-invalid' : '' }}" name="token">--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="form-group row mb-0 login-btn">--}}
{{--<div class="col-md-8 offset-md-4">--}}
{{--<button type="submit" class="btn btn-primary" id="login-btn">--}}
{{--{{ __('Login') }}--}}
{{--</button>--}}
{{--</div>--}}
{{--</div>--}}
{{--</form>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}

<div class="mod-login">
    <div class="card">
        <div class="card-header">@lang("auth.text_login_author")</div>
        <div class="card-body">
            <form id="login-form">
                @csrf

                @if ($errors->has('token'))
                    <h3>
                        <strong>{{ $errors->first('token') }}</strong>
                    </h3>
                    @endif

                    <div class="login_form">
                        @include('front.elements.input.radio-button',
                    ['itemElement'=>'auth.text_login_by_mail', 'name'=>'login_by', 'id'=>'dynamic_mail',
                     'value'=>'email', 'checked'=>'checked', 'class'=>''])

                        @include('front.elements.input.radio-button',
                    ['itemElement'=>'auth.text_login_by_phone', 'name'=>'login_by', 'id'=>'dynamic_phone',
                    'value'=>'phone', 'checked'=>'', 'class'=>''])

                    </div>


                    <div class="form-group row login-by-email login_form type">

                        <input id="email" type="email"
                               class="input-default{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                               value="{{ old('email') }}" autofocus placeholder="@lang('auth.text_mail')">

                    </div>

                    <div class="form-group row login-by-phone login_form type" hidden>
                        <input id="phone" type="text"
                               class="input-default{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                               name="phone"
                               value="{{ old('phone') }}" placeholder="@lang('auth.text_phone')">
                    </div>

                    <div class="login_by">
                        @include('front.elements.input.radio-button',
                            ['itemElement'=>'auth.text_login_mail_permanent', 'name'=>'login_type', 'id'=>'login_mail',
                             'value'=>'permanent', 'checked'=>'checked', 'class'=>''])

                        @include('front.elements.input.radio-button',
                            ['itemElement'=>'auth.text_login_phone_dynamic', 'name'=>'login_type', 'id'=>'login_phone',
                            'value'=>'dynamic', 'checked'=>'', 'class'=>''])
                    </div>

                    <div class="form-group row login-permanent password-type login_form">
                        <input id="password" type="password"
                               class="input-default{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                               placeholder="@lang('auth.text_password')">
                    </div>

                    <div class="form-group row mb-0 login-permanent password-type login_form">
                        @if (Route::has('password.request'))
                            <a class="btn btn-link"
                               href="{{ route('password.request') }}">@lang('auth.text_forgot_your_password')</a>
                            @endif
                    </div>

                    <div class="form-group row temp_password login-dynamic password-type" style="display:none;">
                        <input id="token" type="text"
                               class="input-default{{ $errors->has('token') ? ' is-invalid' : '' }}"
                               name="token" placeholder="@lang('auth.text_temporary_password')">
                    </div>

                    <div class="form-group row login-dynamic password-type" style="display:none;">
                        <a class="btn btn-link" id="get_password" href="#">@lang('auth.text_get_password')</a>
                    </div>
                    @include('front.elements.button.button', ['btnClass'=>'button-blue', 'type'=>'submit','id'=>'login-btn', 'btnText'=>'auth.text_login_btn'])
            </form>
        </div>
    </div>
</div>
