@extends('layouts.app')

@section('title')
  {!! __('statistic_type.show.meta.title', ['type' => $statisticType->name]) !!}
@endsection

@section('content')
  <div class="container-fluid">
    <div class="animated fadeIn">
      @include('coreui-templates::common.errors')
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <div>
                <strong>
                  {{ __('statistic_type.show.title') }}
                </strong>
              </div>
              <div>
                <a href="{{ route('statistics.index') }}" class="btn default">
                  {{ __('statistic_type.show.btn_back') }}
                </a>
                <a href="{{ route('types.edit', ['type' => $statisticType->id]) }} " class="btn btn-light">
                  <span class="cil-pencil btn-icon mr-2"></span>
                  {{ __('statistic_type.show.btn_edit') }}
                </a>
              </div>
            </div>
            <div class="card-body">
              @include('statistic_types.show_fields')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
