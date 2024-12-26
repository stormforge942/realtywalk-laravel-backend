@extends('layouts.app')

@section('title')
  {!! __('setting.builder.meta.title') !!}
@endsection

@section('content')
  <div class="container-fluid">
    <div class="animated fadeIn">
      @include('flash::message')
      @include('coreui-templates::common.errors')

      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <strong>
                {{ __('setting.builder.title') }}
              </strong>
            </div>

            <div class="card-body">
              {!! Form::open([
                    'route'  => 'settings.builder.update',
                    'method' => 'patch'
                  ])
              !!}

                @include('settings.fields.builder')

              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
