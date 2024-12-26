@extends('layouts.app')

@section('title')
  {!! __('statistic.show.meta.title', ['statistic' => $statistic->name]) !!}
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
                  {{ __('statistic.show.title') }}
                </strong>
              </div>
              <div>
                <a href="{{ route('statistics.index') }}" class="btn default">
                  {{ __('statistic.show.btn_back') }}
                </a>
                <a href="{{ route('statistics.edit', ['statistic' => $statistic->id]) }} " class="btn btn-light">
                  <span class="cil-pencil btn-icon mr-2"></span>
                  {{ __('statistic.show.btn_edit') }}
                </a>
              </div>
            </div>
            <div class="card-body">
                @include('statistics.show_fields')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
