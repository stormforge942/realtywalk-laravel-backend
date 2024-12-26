<!-- Title Field -->
<div class="form-group">
  <b>{!! Form::label('title', __('polygon.labels.title')) !!}</b>
  <p>{{ $polygon->title }}</p>
</div>

<!-- Lat Field -->
<div class="form-group">
  <b>{!! Form::label('lat', __('polygon.labels.lat')) !!}</b>
  <p>{!! !is_null($polygon->lat) ? $polygon->lat.'&deg;' : '-' !!}</p>
</div>

<!-- Lng Field -->
<div class="form-group">
  <b>{!! Form::label('lng', __('polygon.labels.lng')) !!}</b>
  <p>{!! !is_null($polygon->lng) ? $polygon->lng.'&deg;' : '-' !!}</p>
</div>

<!-- Color Field -->
<div class="form-group">
  <b>{!! Form::label('color', __('polygon.labels.color')) !!}</b>
  <p>{{ $polygon->color ? '#'.$polygon->color : '-' }}</p>
</div>

<!-- Zoom Field -->
<div class="form-group">
  <b>{!! Form::label('zoom', __('polygon.labels.zoom')) !!}</b>
  <p>{{ $polygon->zoom ?: '-' }}</p>
</div>

<h3 class="mb-0">
  {{ __('polygon.labels.statistics') }}
</h3>

@if($polygon->statistics->count())
  <ol class="pl-3">
  @foreach($statisticTypes as $type)
    @if($type->has_statistic)
      <li><h5 class="mt-2 mb-0">{{ $type->name }}</h5></li>

      <ul class="pl-4">
      @foreach($type->statistics as $statistic)
        <li>
          {{ $statistic->name }}:
          {{ $type->format == '$' ? '$' : '' }}{{ number_format($statistic->value, 2) }}{{ $type->format == '%' ? '%' : '' }}
        </li>
      @endforeach
      </ul>
    @endif
  @endforeach
  </ol>
@else
  <p>{{ __('polygon.labels.statistics_no_data') }}</p>
@endif
