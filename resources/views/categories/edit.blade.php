@extends('layouts.app')

@section('title')
  {!! __('category.edit.meta.title') !!}
@endsection

@section('content')
  <div class="container-fluid">
    <div class="animated fadeIn">
      @include('coreui-templates::common.errors')
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <i class="fa fa-edit fa-lg"></i>
              <strong>
                {{ __('category.edit.title') }}
              </strong>
            </div>
            <div class="card-body">
              {!! Form::model($category, ['route' => ['categories.update', $category->id], 'method' => 'patch']) !!}
              @include('categories.fields')
              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
