@extends('layouts.app_auth')

@section('title')
  {!! __('auth.login.meta.title') !!}
@endsection

@section('content')
  <div class="container">
    <div class="animated fadeIn">
      <div class="row justify-content-center">
        <div class="col-md-8">
          @if(isset($error))
            <p>{{$error}}</p>
          @endif
          <div class="card-group">
            <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
              <div class="card-body">
                <div>
                  <h2>
                    {{ __('auth.login.title') }}
                  </h2>
                  <p class="mb-0">
                    {{ __('auth.login.caption') }}
                  </p>
                </div>
              </div>
            </div>
            <div class="card p-4">
              <div class="card-body">
                <form method="post" action="{{ url('/system/login') }}">
                  @csrf
                  <h1 class="mb-4">
                    {{ __('auth.login.form_title') }}
                  </h1>
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="cil-user"></i>
                      </span>
                    </div>
                    <input type="email" class="form-control {{ $errors->has('email')?'is-invalid':'' }}" name="email" value="{{ old('email') }}" placeholder="{{ __('auth.login.form.placeholders.email') }}">

                    @if ($errors->has('email'))
                    <span class="invalid-feedback">
                      <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                  </div>
                  <div class="input-group mb-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="cil-lock-locked"></i>
                      </span>
                    </div>
                    <input type="password" class="form-control {{ $errors->has('password')?'is-invalid':'' }}"
                      placeholder="Password" name="password">
                    @if ($errors->has('password'))
                    <span class="invalid-feedback">
                      <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                  </div>
                  <div class="mb-4">
                    {!! htmlFormSnippet() !!}
                  </div>
                  <div class="row">
                    <div class="col-6">
                      <button class="btn btn-primary px-4" type="submit">
                        {{ __('auth.login.form.btn_submit') }}
                      </button>
                    </div>
                    <div class="col-6 text-right">
                      <a class="btn btn-link px-0" href="{{ url('system/password/reset') }}">
                        {{ __('auth.login.link_forgot_password') }}
                      </a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
