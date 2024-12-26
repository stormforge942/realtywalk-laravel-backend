@extends('layouts.app')

@section('title')
  {!! __('property_status.edit.meta.title') !!}
@endsection

@section('content')
  <div class="container-fluid">
    <div class="animated fadeIn">
      @include('coreui-templates::common.errors')
      <ul class="errorlist"></ul>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <i class="fa fa-plus-square-o fa-lg"></i>
              <strong>{{ __('property_status.edit.title') }}</strong>
            </div>
            <div class="card-body">
            <form action="{{route('status.update',$propertiesstatus)}}" method="post">
                @csrf
                @method('put')
              @include('propertiesstatus.fields',['status' => $propertiesstatus])
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
