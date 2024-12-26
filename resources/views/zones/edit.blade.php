@extends('layouts.app')

@section('title')
  {!! __('zone.edit.meta.title') !!}
@endsection

@section('content')
  <div class="container-fluid">
     <div class="animated fadeIn">
      @include('coreui-templates::common.errors')
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <i class="fa fa-edit fa-lg"></i>
              <strong>{{ __('zone.edit.title') }}</strong>
            </div>
            <div class="card-body">
              {!! Form::model($zone,
                [
                  'route' => ['zones.update', $zone->id],
                  'method' => 'patch',
                  'class' => 'form-ajax',
                  'data-msg' => __('zone.edit.saved_message'),
                  'data-to' => '/system/polygons/zones'
                ]
              ) !!}

              @include('zones.fields')

              {!! Form::close() !!}
              </div>
            </div>
          </div>
        </div>
     </div>
  </div>
@endsection
