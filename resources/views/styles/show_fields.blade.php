<!-- Name Field -->
<div class="form-group">
    <b>{!! Form::label('name', __('style.labels.name')) !!}</b>
    <p>{{ $style->name }}</p>
</div>

<!-- Descr Field -->
<div class="form-group">
    <b>{!! Form::label('descr', __('style.labels.descr')) !!}</b>
    <p>{{ $style->descr }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    <b>{!! Form::label('created_at', __('style.labels.created_at')) !!}</b>
    <p>{{ $style->created_at->toDayDateTimeString() }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    <b>{!! Form::label('updated_at', __('style.labels.updated_at')) !!}</b>
    <p>{{ $style->updated_at->toDayDateTimeString() }}</p>
</div>
