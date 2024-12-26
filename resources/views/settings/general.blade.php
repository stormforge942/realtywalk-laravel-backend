@extends('layouts.app')

@section('title')
  {!! __('setting.general.meta.title') !!}
@endsection

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
                {{ __('setting.general.title') }}
              </strong>
            </div>
            <div class="card-body">
              {!! Form::open([
                    'route'  => 'settings.index.update',
                    'method' => 'patch',
                    'files'  => true
                  ])
              !!}

                @include('settings.fields.general')

              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
<script>
  var readURL = function (elementID, files) {

    if (files && files[0]) {
      var reader = new FileReader

      reader.onload = function (e) {
        if( document.getElementById(elementID)){
          document.getElementById(elementID).src = e.target.result
        }

      }

      reader.readAsDataURL(files[0])
    }

  }


  var collapsedLogoInput = document.querySelector('.logo-collapsed-upload')
  collapsedLogoInput.addEventListener('change', function (e) {
    readURL('img-logo-collapsed', e.target.files)
  })

  var expandedLogoInput = document.querySelector('.logo-expanded-upload')
  expandedLogoInput.addEventListener('change', function (e) {
    readURL('img-logo-expanded', e.target.files)
  })
</script>
@endsection
