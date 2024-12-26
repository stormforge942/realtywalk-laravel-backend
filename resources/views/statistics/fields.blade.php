<!-- Name Field -->
<div class="form-group col-md-6">
  {!! Form::label('name', __('statistic.labels.name')) !!}
  {!! Form::text('name', null, ['class' => 'form-control','maxlength' => 150]) !!}
</div>

<div class="form-group col-md-6">
  {!! Form::label('role', __('statistic.labels.role')) !!}
  {!! Form::select('role', $types, (isset($statistic) ? $statistic->type_id : null), ['class' => 'form-control']) !!}
</div>

<div class="form-group col-md-9">
  {!! Form::label('descr', __('statistic.labels.descr')) !!}
  {!!Form::textarea("descr", null, ["class" => "desc-editor form-control", 'maxlength' => 500]) !!}
</div>



<!-- Submit Field -->
<div class="form-group col-sm-12">
  {!! Form::submit(__('statistic.labels.btn_submit'), ['class' => 'btn btn-primary']) !!}
  <a href="{{ route('statistics.index') }}" class="btn btn-default">
    {{ __('statistic.labels.btn_cancel') }}
  </a>
</div>
