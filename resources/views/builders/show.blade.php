@extends('layouts.app')

@section('css')
  <link href="{{asset('css/uploader.css')}}" rel="stylesheet" />
@endsection

@section('title')
  {!! __('builder.show.meta.title', ['builder' => $builder->name]) !!}
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
                  {{ __('builder.show.title') }}
                </strong>
              </div>
              <div>
                <a href="{{ route('builders.index') }}" class="btn default">
                  {{ __('builder.show.btn_back') }}
                </a>
                <a href="{{ route('builders.edit', ['builder' => $builder->id]) }} " class="btn btn-light">
                  <span class="cil-pencil btn-icon mr-2"></span>
                  {{ __('builder.show.btn_edit') }}
                </a>
              </div>
            </div>
            <div class="card-body px-3">
              @include('builders.show_fields')
            </div>
          </div>

            @if($builder->aliases_count)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>
                                ALIASES
                            </strong>
                        </div>
                    </div>

                    <div class="card-body px-3">
                        <data-table
                            fetch-url="{{ route('dt.builder-aliases', $builder->id) }}"
                            uri-action="{{ url('system/builders') }}"
                            :fields="[
                  {
                    key: 'name',
                    label: '{{ __('builder.index.table.columns.name') }}',
                    _style: 'width:60%'
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
                ]"></data-table>
                    </div>
                </div>
            @endif

        </div>
      </div>
  </div>
</div>
@endsection
