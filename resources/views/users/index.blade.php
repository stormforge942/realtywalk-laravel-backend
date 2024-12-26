@extends('layouts.app')

@section('title')
  {!! __('user.index.meta.title') !!}
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
                {{ __('user.index.title') }}
              </div>

              <a class="btn btn-ghost-primary pull-right" href="{!! route('users.create') !!}">
                <i class="cil-plus"></i>
                {{ __('user.index.btn_create') }}
              </a>
            </div>
            <div class="card-body">
              <data-table
                fetch-url="{{ route('dt.users') }}"
                uri-action="{{ url('system/users') }}"
                :fields="[
                  { key: 'name', label: '{{ __('user.index.table.columns.name') }}', _style: 'width:30%' },
                  { key: 'email', label: '{{ __('user.index.table.columns.email') }}' },
                  { key: 'role', label: '{{ __('user.index.table.columns.role') }}' },
                  { key: 'created_at', label: '{{ __('user.index.table.columns.created_at') }}' },
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
