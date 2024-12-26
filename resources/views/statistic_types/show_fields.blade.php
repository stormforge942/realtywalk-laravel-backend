<!-- Name Field -->
<div class="form-group">
    <b>{!! Form::label('name', __('statistic_type.labels.name')) !!}</b>
    <p>{{ $statisticType->name }}</p>
</div>

<!-- Statistics Field -->
<div class="form-group">
  <b>{!! Form::label('statistics', __('statistic_type.labels.statistics')) !!}</b>
  @if($statisticType->statistics->count())
    <ul>
      @foreach ($statisticType->statistics as $item)
      <li>{{ $item->name }}</li>
      @endforeach
    </ul>
  @else
    <p>{{ __('statistic_type.labels.statistics_no_data') }}</p>
  @endif
</div>

<!-- Description Field -->
<div class="form-group">
    <b>{!! Form::label('descr', __('statistic_type.labels.descr')) !!}</b>
    <p>{{ $statisticType->descr ?: '-' }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    <b>{!! Form::label('created_at', __('statistic_type.labels.created_at')) !!}</b>
    <p>{{ $statisticType->created_at->toDayDateTimeString() }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    <b>{!! Form::label('updated_at', __('statistic_type.labels.updated_at')) !!}</b>
    <p>{{ $statisticType->updated_at->toDayDateTimeString() }}</p>
</div>
