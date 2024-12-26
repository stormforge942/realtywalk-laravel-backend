@extends('layouts.app_auth')

@section('title')
  {!! __('passwords.reset.meta.title') !!}
@endsection

@section('content')
  <div class="container">
    <div class="animated fadeIn">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card mx-4">
            <div class="card-body p-4">
              <form method="post" action="{{ url('/system/password/reset') }}">
                @csrf
                <h1>
                  {{ __('passwords.reset.title') }}
                </h1>
                <p class="text-muted">
                  {{ __('passwords.reset.caption') }}
                </p>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">@</span>
                  </div>
                  <input
                    type="email"
                    class="form-control {{ $errors->has('email')?'is-invalid':'' }}"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="{{ __('passwords.reset.form.placeholders.email') }}"
                  />

                  @if ($errors->has('email'))
                  <span class="invalid-feedback">
                    <strong>{{ $errors->first('email') }}</strong>
                  </span>
                  @endif
                </div>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="cil-lock-locked"></i>
                    </span>
                  </div>

                  <input
                    type="password"
                    class="form-control {{ $errors->has('password')?'is-invalid':''}}"
                    name="password"
                    placeholder="{{ __('passwords.reset.form.placeholders.password') }}"
                  />

                  @if ($errors->has('password'))
                  <span class="invalid-feedback">
                    <strong>{{ $errors->first('password') }}</strong>
                  </span>
                  @endif
                </div>

                <div class="input-group mb-4">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="cil-lock-locked"></i>
                    </span>
                  </div>

                  <input
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="{{ __('passwords.reset.form.placeholders.password_confirmation') }}"
                  />

                  @if ($errors->has('password_confirmation'))
                  <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                  </span>
                  @endif
                </div>

                <button type="submit" class="btn btn-block btn-primary btn-block btn-flat">
                  {{ __('passwords.reset.form.btn_submit') }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
