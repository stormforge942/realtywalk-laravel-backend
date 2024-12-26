@extends('layouts.app')

@section('title')
  {!! __('user.edit.meta.title') !!}
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
              <i class="fa fa-edit fa-lg"></i>
              <strong>
                {{ __('user.edit.title') }}
              </strong>
            </div>

            <div class="card-body">
              {!! Form::model($user,
                [
                  'route' => ['users.update', $user->id],
                  'method' => 'patch',
                  'class' => 'form-ajax',
                  'data-msg' => __('user.edit.saved_message'),
                  'data-to' => '/system/users'
                ]
              ) !!}

              @include('users.fields')

              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
