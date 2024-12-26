<!-- Name Field -->
<div class="row">
  <div class="form-group col-md-6">
    {!! Form::label('name', __('zone.labels.name')) !!}
    {!! Form::text('name', null, ['class' => 'form-control','maxlength' => 100]) !!}
  </div>
</div>

<!-- Code Field -->
<div class="row">
  <div class="form-group col-md-3">
    {!! Form::label('code', __('zone.labels.code')) !!}
    {!! Form::text('code', null, ['class' => 'form-control','maxlength' => 10]) !!}
  </div>
</div>

<div class="row">
  <!-- Lat Field -->
  <div class="form-group col-sm-6 col-md-3">
    {!! Form::label('lat', __('zone.labels.lat')) !!}

    <input-mask
      name="lat"
      value="{{ isset($zone) && $zone->lat ? $zone->lat : '' }}"
      mask="
        'alias'             : 'numeric',
        'digits'            : 7,
        'autoGroup'         : true,
        'digitsOptional'    : true,
        'min'               : '-90',
        'max'               : '90',
        'suffix'            : '&deg;',
        'autoUnmask'        : true,
        'removeMaskOnSubmit': true,
        'placeholder'       : '0'
      "
    />
  </div>

    <!-- Lng Field -->
  <div class="form-group col-sm-6 col-md-3">
    {!! Form::label('lng', __('zone.labels.lng')) !!}

    <input-mask
      name="lng"
      value="{{ isset($zone) && $zone->lng ? $zone->lng : '' }}"
      mask="
        'alias'             : 'numeric',
        'digits'            : 7,
        'autoGroup'         : true,
        'digitsOptional'    : true,
        'min'               : '-180',
        'max'               : '180',
        'suffix'            : '&deg;',
        'autoUnmask'        : true,
        'removeMaskOnSubmit': true,
        'placeholder'       : '0'
      "
    />
  </div>
</div>

<!-- Parent Tree Select Field -->
<div class="row">
  <div class="form-group col-md-6">
    {!! Form::label('parent_id', __('zone.labels.parent')) !!}

    <tree-select
      fetch-url="{{ route('dtr.zones', ['depth' => 2]) }}"
      name="parent_id"
      placeholder="{{ __('zone.labels.parent_placeholder') }}"
      :selected-options="{{ isset($zone) ? !empty($zone->parent_id)? $zone->parent_id : 'null' : 'null' }}"
    />
  </div>
</div>

<!-- Submit Field -->
<div class="row">
  <div class="form-group col-sm-12">
    {!! Form::submit(__('zone.labels.btn_submit'), ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('zones.index') }}" class="btn btn-default">
      {{ __('zone.labels.btn_cancel') }}
    </a>
  </div>
</div>
