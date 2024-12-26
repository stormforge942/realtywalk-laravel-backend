@extends('layouts.app')

@section('title')
  {!! __('zone.index.meta.title') !!}
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
                {{ __('zone.index.title') }}
              </div>

              <a class="btn btn-ghost-primary pull-right" href="{!! route('zones.create') !!}">
                <i class="cil-plus"></i>
                {{ __('zone.index.btn_create') }}
              </a>
            </div>
            <div class="card-body">
              <data-table
                fetch-url="{{ route('dt.zones') }}"
                uri-action="{{ url('system/polygons/zones') }}"
                :fields="[
                  { key: 'name', label: '{{ __('zone.index.table.columns.name') }}', width: '40%' },
                  { key: 'type', label: '{{ __('zone.index.table.columns.type') }}' },
                  { key: 'lat', label: '{{ __('zone.index.table.columns.lat') }}' },
                  { key: 'lng', label: '{{ __('zone.index.table.columns.lng') }}' },
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
