@extends('layouts.app')

@section('css')
  <link href="{{asset('css/uploader.css')}}" rel="stylesheet" />
@endsection

@section('title')
  {!! __('property.create.meta.title') !!}
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
                {{ __('property.create.title') }}
              </strong>
            </div>
            <div class="card-body">
              {!! Form::open(
                [
                  'route' => 'properties.store',
                  'id' => 'property-create',
                  'class' => 'form-ajax',
                  'files' => true,
                  'enctype'=>'multipart/form-data',
                  'data-msg' => __('property.create.saved_message'),
                  'data-to' => '/system/properties'
                ]
              ) !!}

                  @include('properties.fields')

              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
