@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css">
<link href="{{asset('css/uploader.css')}}" rel="stylesheet" />
@endsection

@section('title')
  {!! __('builder.create.meta.title') !!}
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
                {{ __('builder.create.title') }}
              </strong>
            </div>
            <div class="card-body">
              {!! Form::open(
                [
                  'route' => 'builders.store',
                  'class' => 'form-ajax',
                  'files' => true,
                  'enctype'=>'multipart/form-data',
                  'data-msg' => __('builder.create.saved_message'),
                  'data-to' => '/system/builders'
                ]
              ) !!}

                @include('builders.fields')

              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
