@extends('layouts.app')

@section('title')
  {!! __('profile.meta.title') !!}
@endsection

@section('content')
<div class="container-fluid">
  <div class="animated fadeIn">
    @include('coreui-templates::common.errors')

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <i class="fa fa-user fa-lg"></i>
            <strong>
              {{ __('profile.title') }}
            </strong>
          </div>

          <div class="card-body">
            {!!
              Form::model($user, [
                'route'  => ['profile.update'],
                'method' => 'patch',
                'files'  => true
              ])
            !!}
            <div class="profile-form">
              <div class="profile-pic">
                <div class="img-wrapper">
                  <img id="avatar" class="c-avatar-img"
                    src="{{ Auth::user()->picture_path ?: Avatar::create(Auth::user()->name)->setFontSize(100)->setDimension(400,400)->toBase64() }}"
                    alt="{{ Auth::user()->email }}" />

                  <div class="change-picture">
                    <i class="fa fa-camera fa-lg upload-picture-btn"></i>
                    <input type="file" class="file-upload" name="file" accept="image/*">
                  </div>
                </div>
              </div>
              <div class="profile-inputs">
                <div class="row">
                  <div class="col-lg-7">
                    @include('profile.fields')
                  </div>
                </div>
              </div>
            </div>
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
    var click = function (elem) {
      var evt = new MouseEvent('click', {
        bubbles   : true,
        cancelable: true,
        view      : window
      })

      var canceled = ! elem.dispatchEvent(evt)
    }

    var readURL = function (files) {
      if (files && files[0]) {
        var reader = new FileReader

        reader.onload = function (e) {
          document.getElementById('avatar').src = e.target.result
        }

        reader.readAsDataURL(files[0])
      }
    }

    var uploadBtn = document.querySelector('.upload-picture-btn')
    uploadBtn.addEventListener('click', function (e) {
      click(document.querySelector('.file-upload'))
    })

    var fileInput = document.querySelector('.file-upload')
    fileInput.addEventListener('change', function (e) {
      readURL(e.target.files)
    })
  </script>
@endsection
