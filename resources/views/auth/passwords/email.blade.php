@extends('layouts.app_auth')

@section('title')
  {!! __('passwords.request.meta.title') !!}
@endsection

@section('content')
  <div class="container">
    <div class="animated fadeIn">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card-group">
            <div class="card p-4">
              <div class="card-body">
                @if (session('status'))
                <div class="alert alert-success">
                  {{ session('status') }}
                </div>
                @endif
                <form method="post" action="{{ url('/system/password/email') }}">
                  @csrf
                  <h1>
                    {{ __('passwords.request.title') }}
                  </h1>
                  <p class="text-muted">
                    {{ __('passwords.request.caption') }}
                  </p>
                  <div class="input-group mb-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="cil-user"></i>
                      </span>
                    </div>
                    <input type="email" class="form-control {{ $errors->has('email')?'is-invalid':'' }}" name="email" value="{{ old('email') }}" placeholder="{{ __('passwords.request.form.placeholders.email') }}" />

                    @if ($errors->has('email'))
                    <span class="invalid-feedback">
                      <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                  </div>

                  <div class="mb-4">
                    {!! htmlFormSnippet() !!}
                  </div>

                  <div class="row flex-row align-items-center">
                    <div class="col-6">
                      <a href="{{ route('login') }}">
                        {{ __('passwords.request.link_remember') }}
                      </a>
                    </div>
                    <div class="col-6">
                      <button class="btn btn-block btn-primary" type="submit">
                        {{ __('passwords.request.form.btn_submit') }}
                      </button>
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
