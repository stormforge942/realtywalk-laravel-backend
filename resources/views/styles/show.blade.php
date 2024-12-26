@extends('layouts.app')

@section('title')
  {!! __('style.show.meta.title', ['style' => $style->name]) !!}
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
                  {{ __('style.show.title') }}
                </strong>
              </div>
              <div>
                <a href="{{ route('styles.index') }}" class="btn default">
                  {{ __('style.show.btn_back') }}
                </a>
                <a href="{{ route('styles.edit', ['style' => $style->id]) }} " class="btn btn-light">
                  <span class="cil-pencil btn-icon mr-2"></span>
                  {{ __('style.show.btn_edit') }}
                </a>
              </div>
            </div>
            <div class="card-body">
              @include('styles.show_fields')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
