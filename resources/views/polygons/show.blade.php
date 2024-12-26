@extends('layouts.app')

@section('title')
  {!! __('polygon.show.meta.title', ['polygon' => $polygon->title]) !!}
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
                    {{ __('polygon.show.title') }}
                  </strong>
                </div>
                <div>
                  <a href="{{ route('polygons.index') }}" class="btn default">
                    {{ __('polygon.show.btn_back') }}
                  </a>
                  <a href="{{ route('polygons.edit', ['polygon' => $polygon->id]) }} " class="btn btn-light">
                    <span class="cil-pencil btn-icon mr-2"></span>
                    {{ __('polygon.show.btn_edit') }}
                  </a>
                </div>
              </div>
               <div class="card-body">
                 @include('polygons.show_fields')
               </div>
             </div>
           </div>
         </div>
      </div>
  </div>
@endsection
