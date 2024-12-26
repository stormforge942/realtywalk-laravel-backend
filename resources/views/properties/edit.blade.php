@extends('layouts.app')

@section('css')
  <link href="{{asset('css/uploader.css')}}" rel="stylesheet" />
@endsection

@section('title')
  {!! __('property.edit.meta.title') !!}
@endsection

@section('content')
  <div class="container-fluid">
    <div class="animated fadeIn">
      @include('coreui-templates::common.errors')

      <div class="row">
        <div class="col-lg-12">
          @if($property->is_uploading_files)
            <div ref="uploadAlert" data-check-uri="{{ url('/system/upload-process-status?model=property&id='.$property->id) }}" class="alert alert-warning alert-dismissible fade show" role="alert">
              <strong>Information!</strong>
              The file upload is still in process. You can't add or delete any files for this record until the upload process is finished. You can refresh this page later.
            </div>
          @endif

          @if($uploaded_notice ?? false)
            <div class="alert alert-info alert-dismissible fade show" role="alert">
              <strong>File uploaded!</strong>
              The files has successfully uploaded.

              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endif

          <ul class="errorlist"></ul>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <i class="fa fa-edit fa-lg"></i>
              <strong>
                {{ __('property.edit.title') }}
              </strong>
            </div>
            <div class="card-body">
              {!! Form::model($property,
                [
                  'route' => ['properties.update', $property->id],
                  'method' => 'patch',
                  'class' => 'form-ajax',
                  'data-msg' => __('property.edit.saved_message'),
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
