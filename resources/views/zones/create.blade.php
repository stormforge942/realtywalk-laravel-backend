@extends('layouts.app')

@section('title')
  {!! __('zone.create.meta.title') !!}
@endsection

@section('content')
   <div class="container-fluid">
      <div class="animated fadeIn">
        @include('coreui-templates::common.errors')
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <i class="fa fa-plus-square-o fa-lg"></i>
                <strong>
                  {{ __('zone.create.title') }}
                </strong>
              </div>
              <div class="card-body">
                {!! Form::open(
                  [
                    'route' => 'zones.store',
                    'class' => 'form-ajax',
                    'data-msg' => __('zone.create.saved_message'),
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
