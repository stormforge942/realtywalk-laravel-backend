<!-- Name Field -->
<div class="form-group col-md-6">
    {!! Form::label('name', __('style.labels.name')) !!}
    {!! Form::text('name', null, ['class' => 'form-control','maxlength' => 150]) !!}
</div>

<!-- Descr Field -->
<div class="form-group col-md-9">
    {!! Form::label('descr', __('style.labels.descr')) !!}
    {!! Form::textarea('descr', null, ['class' => 'desc-editor form-control', 'maxlenght' => 500]) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('style.labels.btn_submit'), ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('styles.index') }}" class="btn btn-default">
        {{ __('style.labels.btn_cancel') }}
    </a>
</div>
