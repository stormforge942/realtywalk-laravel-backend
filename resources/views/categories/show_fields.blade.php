<!-- Name Field -->
<div class="form-group">
    <b>{!! Form::label('name', __('category.labels.name')) !!}</b>
    <p>{{ $category->name }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    <b>{!! Form::label('created_at', __('category.labels.created_at')) !!}</b>
    <p>{{ $category->created_at->toDayDateTimeString() }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    <b>{!! Form::label('updated_at', __('category.labels.updated_at')) !!}</b>
    <p>{{ $category->updated_at->toDayDateTimeString() }}</p>
</div>
