@extends('layouts.app')

@section('title')
  {!! __('setting.script.meta.title') !!}
@endsection

@push('extra-css')
<style>
textarea {
  border:1px solid #999999;
  max-width:98%;
  margin:5px 0;
  padding:1%;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
  <div class="animated fadeIn">
    @include('flash::message')
    @include('coreui-templates::common.errors')

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <strong>
              {{ __('setting.script.title') }}
            </strong>
          </div>
          <div class="card-body">
            <form action="{{route('settings.update.scripts')}}" method="post">
              @csrf
              @method('put')
              <div class="form-group col-12">
                <label>
                  {{ __('setting.script.labels.header_script') }}
                </label>
              </div>

              <div class="form-group col-12">
                <textarea clas="form-control textarea" name="header_scripts" rows="10" cols="150">
                  {!!$header_scripts!!}
                </textarea>
              </div>

              <div class="form-group col-md-12">
                <label>
                  {{ __('setting.script.labels.footer_script') }}
                </label>
              </div>

              <div class="form-group col-12">
                <textarea clas="form-control textarea" name="footer_scripts" rows="10" cols="150">
                  {!!$footer_scripts!!}
                </textarea>
              </div>

              <div class="form-group col-sm-12">
                <button class="btn btn-primary" type="submit">
                  {{ __('setting.script.btn_submit') }}
                </button>
                <a href="{!! url()->current() !!}" class="btn btn-default">
                  {{ __('setting.script.btn_cancel') }}
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
