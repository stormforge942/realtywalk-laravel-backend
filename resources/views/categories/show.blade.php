@extends('layouts.app')

@section('title')
  {!! __('category.show.meta.title', ['category' => $category->name]) !!}
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
                    {{ __('category.show.title') }}
                  </strong>
                </div>
                <div>
                  <a href="{{ route('categories.index') }}" class="btn default">
                    {{ __('category.show.btn_back') }}
                  </a>
                  <a href="{{ route('categories.edit', ['category' => $category->id]) }} " class="btn btn-light">
                    <span class="cil-pencil btn-icon mr-2"></span>
                    {{ __('category.show.btn_edit') }}
                  </a>
                </div>
              </div>
              <div class="card-body">
                @include('categories.show_fields')
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
@endsection
