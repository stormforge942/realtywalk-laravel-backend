<!-- Name Field -->
<div class="form-group">
  <b>{!! Form::label('name', __('statistic.labels.name')) !!}</b>
  <p>{{ $statistic->name }}</p>
</div>

<!-- Type Field -->
<div class="form-group">
  <b>{!! Form::label('type', __('statistic.labels.type')) !!}</b>
  <p>{{ $statistic->type->name }}</p>
</div>

<!-- Description Field -->
<div class="form-group">
  <b>{!! Form::label('descr', __('statistic.labels.descr')) !!}</b>
  <p>{{ $statistic->descr ?: '-' }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
  <b>{!! Form::label('created_at', __('statistic.labels.created_at')) !!}</b>
  <p>{{ $statistic->created_at->toDayDateTimeString() }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
  <b>{!! Form::label('updated_at', __('statistic.labels.updated_at')) !!}</b>
  <p>{{ $statistic->updated_at->toDayDateTimeString() }}</p>
</div>
