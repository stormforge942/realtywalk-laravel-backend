@extends('layouts.app')

@section('css')
  <link href="{{asset('css/uploader.css')}}" rel="stylesheet" />
@endsection

@section('title')
  {!! __('property.show.meta.title', ['property' => $property->title]) !!}
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
                    {{ __('property.show.title') }}
                  </strong>
                </div>
                <div>
                  <a href="{{ route('properties.index') }}" class="btn default">
                    {{ __('property.show.btn_back') }}
                  </a>
                  <a href="{{ route('properties.edit', ['property' => $property->id]) }} " class="btn btn-light">
                    <span class="cil-pencil btn-icon mr-2"></span>
                    {{ __('property.show.btn_edit') }}
                  </a>
                </div>
              </div>
               <div class="card-body p-3">
                 @include('properties.show_fields')
               </div>
             </div>
           </div>
         </div>
      </div>
  </div>
@endsection
