<!-- Name Field -->
<div class="form-group">
  {!! Form::label('name', __('profile.labels.name')) !!}
  {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
</div>

<!-- Email Field -->
<div class="form-group">
  {!! Form::label('email', __('profile.labels.email')) !!}

  {!! Form::text('email', null, [
        'v-input-mask', 'class' => 'form-control',
        'data-inputmask' => "
          'alias': 'email',
          'clearIncomplete': true
        ",
        'maxlength' => 128
      ])
  !!}
</div>

<!-- Password Field -->
<div class="form-group">
  {!! Form::label('password', __('profile.labels.email')) !!}
  {!! Form::password('password', ['class' => 'form-control']) !!}
</div>

<!-- Confirmation Password Field -->
<div class="form-group">
  {!! Form::label('password', __('profile.labels.password_confirmation')) !!}
  {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
  {!! Form::submit(__('profile.btn_submit'), ['class' => 'btn btn-primary']) !!}
  <a href="{!! route('dashboard') !!}" class="btn btn-default">
  {{ __('profile.btn_cancel') }}
  </a>
</div>
