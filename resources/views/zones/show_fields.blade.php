<!-- Name Field -->
<div class="form-group">
  <b>{!! Form::label('name', __('zone.labels.name')) !!}</b>
  <p>{{ $zone->name }}</p>
</div>

<!-- Lat Field -->
<div class="form-group">
  <b>{!! Form::label('lat', __('zone.labels.lat')) !!}</b>
  <p>{{ !is_null($zone->lat) ? $zone->lat : '-' }}&deg;</p>
</div>

<!-- Lng Field -->
<div class="form-group">
  <b>{!! Form::label('lng', __('zone.labels.lng')) !!}</b>
  <p>{{ !is_null($zone->lng) ? $zone->lat : '-' }}&deg;</p>
</div>
