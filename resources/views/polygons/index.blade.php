@extends('layouts.app')

@section('title')
  {!! __('polygon.index.meta.title') !!}
@endsection

@section('content')
  <div class="container-fluid">
    <div class="animated fadeIn">
      @include('flash::message')

      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <div class="mt-2 pull-left">
                <i class="cil-list-rich"></i>
                {{ __('polygon.index.title') }}
              </div>

              <a class="btn btn-ghost-primary pull-right" href="{!! route('polygons.create') !!}">
                <i class="cil-plus"></i>
                {{ __('polygon.index.btn_create') }}
              </a>
            </div>
            <div class="card-body">

              <data-table
                fetch-url="{{ route('dt.polygons') }}"
                uri-action="{{ url('system/polygons') }}"
                :chunkLoad="true"
                :fields="[
                  {
                    key: 'title',
                    label: '{{ __('polygon.index.table.columns.title') }}',
                    width: '50%'
                  },
                  {
                    key: 'zoom',
                    label: '{{ __('polygon.index.table.columns.zoom') }}',
                    _classes: 'text-right'
                  },
                  {
                    key: 'zone_name',
                    label: '{{ __('polygon.index.table.columns.zone') }}'
                  },
                  {
                    key: 'actions',
                    label: '',
                    _style: 'width:1%',
                    sorter: false,
                    filter: false
                  }
                ]"
              ></data-table>
            </div>
          </div>
        </div>
       </div>
     </div>
  </div>
@endsection
