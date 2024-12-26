@extends('layouts.app')

@section('title')
  {!! __('amenity.show.meta.title', ['amenity' => $amenity->name]) !!}
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
                  {{ __('amenity.show.title') }}
                </strong>
              </div>
              <div>
                <a href="{{ route('amenities.index') }}" class="btn default">
                  {{ __('amenity.show.btn_back') }}
                </a>
                <a href="{{ route('amenities.edit', ['amenity' => $amenity->id]) }} " class="btn btn-light">
                  <span class="cil-pencil btn-icon mr-2"></span>
                  {{ __('amenity.show.btn_edit') }}
                </a>
              </div>
            </div>
            <div class="card-body">
              @include('amenities.show_fields')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
