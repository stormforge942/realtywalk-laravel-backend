@extends('layouts.app')

@section('css')
  <link href="{{asset('css/uploader.css')}}" rel="stylesheet" />
@endsection

@section('title')
  {!! __('property.index.meta.title') !!}
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
                {{ __('property.index.title') }}
              </div>

              <a class="btn btn-ghost-primary pull-right" href="{!! route('properties.create') !!}">
                <i class="cil-plus"></i>
                {{ __('property.index.btn_create') }}
              </a>
            </div>
            <div class="card-body">
              <data-table
                fetch-url="{{ route('dt.properties') }}"
                view-uri="{{ url('property') }}"
                uri-action="{{ url('system/properties') }}"
                :fields="[
                  {
                    key: 'title',
                    label: '{{ __('property.index.table.columns.title') }}',
                    _style: 'width:30%'
                  },
                  {
                    key: 'builder_name',
                    label: '{{ __('property.index.table.columns.builder_name') }}'
                  },
                  {
                    key: 'full_address',
                    label: '{{ __('property.index.table.columns.address') }}'
                  },
                  {
                    key: 'updated_at',
                    label: '{{ __('property.index.table.columns.updated_at') }}',
                    _style: 'width: 220px;'
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
