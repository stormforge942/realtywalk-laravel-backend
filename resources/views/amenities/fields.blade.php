<!-- Name Field -->
<div class="form-group col-md-6">
  {!! Form::label('name', __('amenity.labels.name')) !!}
  {!! Form::text('name', null, ['class' => 'form-control','maxlength' => 150]) !!}
</div>

<!-- Description Field -->
<div class="form-group col-md-9">
  {!! Form::label('description', __('amenity.labels.description')) !!}
  {!!Form::textarea("description", null, ["class" => "desc-editor form-control", 'maxlength' => 500]) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
  {!! Form::submit(__('amenity.labels.btn_submit'), ['class' => 'btn btn-primary']) !!}
  <a href="{{ route('amenities.index') }}" class="btn btn-default">
    {{ __('amenity.labels.btn_cancel') }}
  </a>
</div>
