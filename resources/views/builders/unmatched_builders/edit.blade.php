@extends('layouts.app')

@section('title')
  {!! __('builder.unmatched_edit.meta.title') !!}
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
              <strong>{{ __('builder.unmatched_edit.title') }}</strong>
            </div>
            <div class="card-body">
              {!! Form::model($unmatchedBuilder, [
                'route' => ['unmatched_builders.update', $unmatchedBuilder->id],
                'method' => 'patch',
                'class' => 'form-ajax',
                'data-msg' => __('builder.unmatched_edit.saved_message'),
                'data-to' => '/system/builders'
              ]) !!}

                <div class="form-group col-md-6">
                  <b>{!! Form::label('name', __('builder.unmatched_labels.name')) !!}</b>
                  <p>{{ $unmatchedBuilder->name }}</p>
                </div>

                <!-- Builder Field -->
                <div class="form-group col-md-6">
                  {!! Form::label('builder_id', __('builder.unmatched_labels.builder')) !!}

                  <tree-select-builder-alias
                    name="builder_id"
                    :similars="{{json_encode(collect($similars ?? [])->toArray())}}"
                    alias-id="{{ $unmatchedBuilder?->builder_id }}"
                  />

                  <p class="mt-1">
                    Type to search if you can't find the builder in the select field above.
                  </p>
                </div>

                <div class="form-group col-sm-12">
                  {!! Form::submit(__('builder.unmatched_labels.btn_submit'), ['class' => 'btn btn-primary']) !!}
                  <a href="{{ route('builders.index') }}" class="btn btn-default">
                    {{ __('builder.unmatched_labels.btn_cancel') }}
                  </a>
                </div>

              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
