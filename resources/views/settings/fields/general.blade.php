{{-- Site Title Field --}}
@php
$site_title = $settings->getBy('site_title');
@endphp
<div class="row">
  <div class="form-group col-md-6">
    {!! Form::label('site_title', __('setting.general.labels.site_title')) !!}
    {!! Form::text('site_title', $site_title, ['class' => 'form-control','maxlength' => 50]) !!}
  </div>
</div>

{{-- Site Logo Collapsed --}}
<div class="row">
  <div class="form-group col-md-6">
    {!! Form::label('site_logo_collapsed', __('setting.general.labels.site_logo_collapsed')) !!}
    @if($site_logo_collapsed = $settings->getLogo(true))
    <div class="mt-1">
      <img id="img-logo-collapsed" src="{{ $site_logo_collapsed }}" style="max-width: 75px" />
    </div>
    @endif
    <br>
    {!! Form::file('site_logo_collapsed', ['class' => 'logo-collapsed-upload', 'accept' => 'image/*']) !!}
    <p class="text-muted mt-1 mb-0">
      {{ __('setting.general.labels.site_logo_collapsed_help') }}
    </p>
  </div>
</div>

{{-- Site Logo Expanded --}}
<div class="row">
  <div class="form-group col-md-6">
    {!! Form::label('site_logo_expanded', __('setting.general.labels.site_logo_expanded')) !!}
    @if($site_logo_expanded = $settings->getLogo())
    <div class="mt-1">
      <img id="img-logo-expanded" src="{{ $site_logo_expanded }}" style="max-width: 300px" />
    </div>
    @endif
    <br>
    {!! Form::file('site_logo_expanded', ['class' => 'logo-expanded-upload', 'accept' => 'image/*']) !!}
    <p class="text-muted mt-1 mb-0">
      {{ __('setting.general.labels.site_logo_expanded_help') }}
    </p>
  </div>
</div>

 {{-- Terms of Service --}}
<div class="row">
  <div class="form-group col-12">
    {!! Form::label('terms_of_service', __('setting.general.labels.terms_of_service')) !!}
    {!! Form::textarea("terms_of_service", $settings->getBy('terms_of_service'), ["class" => "desc-editor form-control", 'maxlength' => 10000]) !!}
  </div>
</div>

<div class="row">
  <div class="form-group col-12">
    {!! Form::label('privacy_policy', __('setting.general.labels.privacy_policy')) !!}
    {!! Form::textarea("privacy_policy", $settings->getBy('privacy_policy'), ["class" => "desc-editor form-control", 'maxlength' => 10000]) !!}
  </div>
</div>

<!-- Submit Field -->
<div class="row">
  <div class="form-group col-sm-12 mt-4">
    {!! Form::submit(__('setting.general.btn_submit'), ['class' => 'btn btn-primary']) !!}
    <a href="{!! url()->current() !!}" class="btn btn-default">
      {{ __('setting.general.btn_cancel') }}
    </a>
  </div>
</div>
