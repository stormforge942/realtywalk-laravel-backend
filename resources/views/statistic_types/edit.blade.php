@extends('layouts.app')

@section('title')
  {!! __('statistic_type.edit.meta.title') !!}
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
              <i class="fa fa-edit fa-lg"></i>
              <strong>
                {{ __('statistic_type.edit.title') }}
              </strong>
            </div>
            <div class="card-body">
              {!! Form::model($statisticType,
                [
                  'route' => ['types.update', $statisticType->id],
                  'method' => 'patch',
                  'class' => 'form-ajax',
                  'data-msg' => __('statistic_type.edit.saved_message'),
                  'data-to' => '/system/polygons/statistics'
                ]
              ) !!}

                @include('statistic_types.fields')

              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
