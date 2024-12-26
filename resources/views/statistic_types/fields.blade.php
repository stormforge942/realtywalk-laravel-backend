<!-- Name Field -->
<div class="form-group col-md-6">
  {!! Form::label('name', __('statistic_type.labels.name')) !!}
  {!! Form::text('name', null, ['class' => 'form-control','maxlength' => 150]) !!}
</div>

<!-- Name Field -->
@php
$formats = [
  '%' => __('statistic_type.labels.option_percent'),
  'n' => __('statistic_type.labels.option_number'),
  '$' => __('statistic_type.labels.option_currency'),
];
@endphp
<div class="form-group col-sm-4 col-md-3">
  {!! Form::label('format', __('statistic_type.labels.format')) !!}
  {!! Form::select('format', $formats, isset($statisticType) ? $statisticType->format : null,['class' => 'form-control']) !!}
</div>

<!-- Descr Field -->
{{--
<div class="form-group col-md-9">
  {!! Form::label('descr', 'Descr:') !!}
  {!! Form::textarea('descr', null, ['class' => 'form-control','maxlength' => 500]) !!}
</div>
--}}

<div class="form-group col-md-9">
  {!! Form::label('descr', __('statistic_type.labels.descr')) !!}
  {!!Form::textarea("descr", null, ["class" => "desc-editor form-control", 'maxlength' => 500 ]) !!}
</div>




<!-- Submit Field -->
<div class="form-group col-sm-12">
  {!! Form::submit(__('statistic_type.labels.btn_submit'), ['class' => 'btn btn-primary']) !!}
  <a href="{{ route('statistics.index') }}" class="btn btn-default">
    {{ __('statistic_type.labels.btn_cancel') }}
  </a>
</div>
