<input placeholder="имя" class="order_name" type="text" name="name" value="{{ old('name') }}">
@if ($errors->has('name'))
    <span class="invalid-feedback" role="alert">
       <strong>{{ $errors->first('name') }}</strong>
     </span>
@endif
<input placeholder="фамилия" class="order_last_name" type="text" name="last_name" value="{{ old('last_name') }}">
    @if ($errors->has('last_name'))
        <span class="invalid-feedback" role="alert">
       <strong>{{ $errors->first('last_name') }}</strong>
     </span>
    @endif
<input placeholder="почта" class="order_email" type="email" name="email" value="{{ old('email') }}">
@if ($errors->has('email'))
    <span class="invalid-feedback" role="alert">
       <strong>{{ $errors->first('email') }}</strong>
     </span>
@endif
<input placeholder="телефон" class="order_phone" type="text" name="phone" value="{{ old('phone') }}">
@if ($errors->has('phone'))
    <span class="invalid-feedback" role="alert">
       <strong>{{ $errors->first('phone') }}</strong>
     </span>
@endif
