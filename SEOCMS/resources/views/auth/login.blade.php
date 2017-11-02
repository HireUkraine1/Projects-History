@extends('layouts.login')

@section('form')
    <form method="POST">
        {{ csrf_field() }}
        <h1>{{ __('auth/login.login_form') }}</h1>
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input type="text" class="form-control " placeholder="{{ __('auth/login.placeholder_username') }}" name="email" value="{{ old('email') }}" required autofocus />
            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <input type="password" class="form-control" placeholder="{{ __('auth/login.placeholder_password') }}" name="password" required />
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>
        <div>
            <button type="submit" class="btn btn-default submit">
                {{ __('auth/login.login') }}
            </button>
            <a class="reset_pass" href="{{ url('/password/reset') }}">{{ __('auth/login.lost_password') }}</a>
        </div>
        <div class="clearfix"></div>
        <div class="separator">
            <div class="clearfix"></div>
            <br/>
            <div>
                <h1>{{ config('app.name') }}</h1>
                <p>{{ __('auth/login.rights') }}</p>
            </div>
        </div>
    </form>
@endsection