<!-- Name Field -->
<div class="form-group col-md-6">
    {!! Form::label('name', __('category.labels.name')) !!}
    {!! Form::text('name', null, ['class' => 'form-control','maxlength' => 150]) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('category.labels.btn_submit'), ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('categories.index') }}" class="btn btn-default">
        {{ __('category.labels.btn_cancel') }}
    </a>
</div>
