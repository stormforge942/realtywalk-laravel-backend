@extends('layouts.app')

@section('title')
  {!! __('zone.show.meta.title', ['zone' => $zone->name]) !!}
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
                    {{ __('zone.show.title') }}
                  </strong>
                </div>
                <div>
                  <a href="{{ route('zones.index') }}" class="btn default">
                    {{ __('zone.show.btn_back') }}
                  </a>
                  <a href="{{ route('zones.edit', ['zone' => $zone->id]) }} " class="btn btn-light">
                    <span class="cil-pencil btn-icon mr-2"></span>
                    {{ __('zone.show.btn_edit') }}
                  </a>
                </div>
              </div>
              <div class="card-body">
                @include('zones.show_fields')
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
@endsection
