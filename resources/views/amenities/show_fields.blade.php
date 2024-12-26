<!-- Name Field -->
<div class="form-group">
  <b>{!! Form::label('name', __('amenity.labels.name')) !!}</b>
  <p>{{ $amenity->name }}</p>
</div>

<!-- Descr Field -->
<div class="form-group">
  <b>{!! Form::label('descr', __('amenity.labels.description')) !!}</b>
  <p>{{ $amenity->descr }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
  <b>{!! Form::label('created_at', __('amenity.labels.created_at')) !!}</b>
  <p>{{ $amenity->created_at->toDayDateTimeString() }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
  <b>{!! Form::label('updated_at', __('amenity.labels.updated_at')) !!}</b>
  <p>{{ $amenity->updated_at->toDayDateTimeString() }}</p>
</div>
