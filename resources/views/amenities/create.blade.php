@extends('layouts.app')

@section('title')
  {!! __('amenity.create.meta.title') !!}
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
                {{ __('amenity.create.title') }}
              </strong>
            </div>
            <div class="card-body">
              {!! Form::open(
                [
                  'route' => 'amenities.store',
                  'class' => 'form-ajax',
                  'data-msg' => __('amenity.create.saved_message'),
                  'data-to' => '/system/properties/amenities'
                ]
              ) !!}

                @include('amenities.fields')

              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
