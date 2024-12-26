@extends('layouts.app')

@section('title')
  {!! __('amenity.index.meta.title') !!}
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
                {{ __('amenity.index.title') }}
              </div>

              <a class="btn btn-ghost-primary pull-right" href="{!! route('amenities.create') !!}">
                <i class="cil-plus"></i>
                {{ __('amenity.index.btn_create') }}
              </a>
            </div>
            <div class="card-body">
              <data-table
                fetch-url="{{ route('dt.amenities') }}"
                uri-action="{{ url('system/properties/amenities') }}"
                :fields="[
                  { key: 'name', label: '{{ __('amenity.index.table.columns.name') }}' },
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
