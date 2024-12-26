@php
$blacklist_names =  $settings['blacklist_names'] ?? null;
$builder_link_enabled = $settings['builder_link_enabled'] ?? true;
@endphp

<div class="row">
  <div class="form-group col-sm-6 col-md-4">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="builder_link_enabled" value="1" id="flexCheckChecked"{!! $builder_link_enabled ? ' checked' : '' !!}>
      <label class="form-check-label" for="flexCheckChecked">
        {{ __('setting.builder.labels.builder_link_enabled') }}
      </label>
    </div>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-6 col-md-4">
    {!! Form::label('site_title', __('setting.builder.labels.blacklist_names')) !!}
    {!! Form::textarea('blacklist_names', $blacklist_names, ['class' => 'form-control']) !!}
  </div>
</div>

@php
$generic_names =  $settings['generic_names'] ?? null;
@endphp

<div class="row">
  <div class="form-group col-sm-6 col-md-4">
    {!! Form::label('site_title', __('setting.builder.labels.generic_names')) !!}
    {!! Form::textarea('generic_names', $generic_names, ['class' => 'form-control']) !!}
  </div>
</div>

<!-- Submit Field -->
<div class="row">
  <div class="form-group col-sm-12 mt-4">
    {!! Form::submit(__('setting.builder.btn_submit'), ['class' => 'btn btn-primary']) !!}
    <a href="{!! url()->current() !!}" class="btn btn-default">
      {{ __('setting.builder.btn_cancel') }}
    </a>
  </div>
</div>
