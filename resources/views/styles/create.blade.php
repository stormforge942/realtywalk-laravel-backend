@extends('layouts.app')

@section('title')
  {!! __('style.create.meta.title') !!}
@endsection

@section('content')
  <div class="container-fluid">
    <div class="animated fadeIn">
      @include('coreui-templates::common.errors')
      <ul class="errorlist"></ul>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <i class="fa fa-plus-square-o fa-lg"></i>
              <strong>
                {{ __('style.create.title') }}
              </strong>
            </div>
            <div class="card-body">
              {!! Form::open(
                [
                  'route' => 'styles.store',
                  'class' => 'form-ajax',
                  'data-msg' => __('style.create.saved_message'),
                  'data-to' => '/system/properties/styles'
                ]
              ) !!}

                @include('styles.fields')

              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
