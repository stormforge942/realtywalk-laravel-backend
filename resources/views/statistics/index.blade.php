@extends('layouts.app')

@section('title')
  {!! __('statistic.index.meta.title') !!}
@endsection

@section('content')
  <div class="container-fluid">
    <div class="animated fadeIn">
      @include('flash::message')
      <div class="row">
        <div class="col-lg-7">
          <div class="card">
            <div class="card-header">
              <div class="mt-2 pull-left">
                <i class="cil-list-rich"></i>
                {{ __('statistic.index.statistic.title') }}
              </div>

              <a class="btn btn-ghost-primary pull-right" href="{!! route('statistics.create') !!}">
                <i class="cil-plus"></i>
                {{ __('statistic.index.statistic.btn_create') }}
              </a>
            </div>
            <div class="card-body">
              <data-table
                fetch-url="{{ route('dt.statistics') }}"
                uri-action="{{ url('system/polygons/statistics') }}"
                :fields="[
                  {
                    key: 'name',
                    label: '{{ __('statistic.index.statistic.table.columns.name') }}'
                  },
                  {
                    key: 'type_name',
                    label: '{{ __('statistic.index.statistic.table.columns.type_name') }}'
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

        <div class="col-lg-5">
          <div class="card">
            <div class="card-header">
              <div class="mt-2 pull-left">
                <i class="cil-list-rich"></i>
                {{ __('statistic.index.type.title') }}
              </div>

              <a class="btn btn-ghost-primary pull-right" href="{!! route('types.create') !!}">
                <i class="cil-plus"></i>
                {{ __('statistic.index.type.btn_create') }}
              </a>
            </div>
            <div class="card-body">
              <data-table
                fetch-url="{{ route('dt.statistic_types') }}"
                uri-action="{{ url('system/polygons/statistics/types') }}"
                :fields="[
                  {
                    key: 'name',
                    label: '{{ __('statistic.index.type.table.columns.name') }}'
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
