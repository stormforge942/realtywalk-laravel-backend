@extends('layouts.app')

@section('title')
  {!! __('property_status.create.meta.title') !!}
@endsection

@section('content')
  <div class="container-fluid">
    <div class="animated fadeIn">
      @include('flash::message')
      <ul class="errorlist"></ul>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <i class="fa fa-plus-square-o fa-lg"></i>
              <strong>
                {{ __('property_status.create_title') }}
              </strong>
            </div>
            <div class="card-body">
            <form action="{{route('status.store')}}" method="post">
                @csrf
                @include('propertiesstatus.fields')
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
