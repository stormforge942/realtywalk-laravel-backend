@extends('layouts.app')

@section('title')
  {!! __('builder.index.meta.title') !!}
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
                {{ __('builder.index.title') }}
              </div>

              <a class="btn btn-ghost-primary pull-right" href="{!! route('builders.create') !!}">
                <i class="cil-plus"></i>
                {{ __('builder.index.btn_create') }}
              </a>
            </div>
            <div class="card-body">
                <input type="checkbox" name="builder_type" id="toggle_aliased" {{$showAliased ? 'checked' : ''}}> Show Aliased
                <data-table
                fetch-url="{{ route('dt.builders', ['aliased' => $showAliased]) }}"
                uri-action="{{ url('system/builders') }}"
                :fields="[
                  {
                    key: 'id',
                    label: '{{ __('builder.index.table.columns.id') }}',
                    _style: 'width:100px'
                  },
                  {
                    key: 'name',
                    label: '{{ __('builder.index.table.columns.name') }}',
                    _style: 'width:40%'
                  },
                  {
                    key: 'aliases_count',
                    label: '{{ __('builder.index.table.columns.aliases_count') }}',
                    _style: '',
                    sorter: true,
                    filter: false
                  },
                  {
                    key: 'properties_count',
                    label: '{{ __('builder.index.table.columns.properties_count') }}',
                    _style: '',
                    sorter: true,
                    filter: false
                  },
                  {
                    key: 'updated_at',
                    label: '{{ __('builder.index.table.columns.updated_at') }}'
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

    <div class="animated fadeIn">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="mt-2 pull-left">
                  <i class="cil-list-rich"></i>

                  {{ __('builder.index.unmatched_builders.title') }}
                </div>
              </div>
              <div class="card-body">
                <input type="checkbox" name="unmatched_filter" id="toggle_hide_blacklist" {{$hideBlacklist ? 'checked' : ''}} /> Hide Blacklist

                <data-table
                  fetch-url="{{ route('dt.unmatched_builders', ['hide_blacklist' => $hideBlacklist]) }}"
                  uri-action="{{ url('system/unmatched_builders/') }}"
                  :fields="[
                    {
                      key: 'name',
                      label: '{{ __('builder.index.unmatched_builders.table.columns.name') }}',
                      _style: 'width:40%'
                    },
                    {
                      key: 'builder_name',
                      label: '{{ __('builder.index.unmatched_builders.table.columns.builder') }}',
                    },
                    {
                      key: 'updated_at',
                      label: '{{ __('builder.index.unmatched_builders.table.columns.updated_at') }}',
                      filter: false
                    }, {
                      key: 'actions',
                      label: '',
                      _style: 'width:70px',
                      sorter: false,
                      filter: false
                    }
                  ]"
                />
              </div>
            </div>
          </div>
        </div>
     </div>
  </div>
@endsection
