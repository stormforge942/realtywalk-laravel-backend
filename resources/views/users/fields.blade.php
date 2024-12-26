<!-- Name Field -->
<div class="form-group col-md-6">
  {!! Form::label('name', __('user.labels.name')) !!}
  {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
</div>

<!-- Email Field -->
<div class="form-group col-md-6">
  {!! Form::label('email', __('user.labels.email')) !!}
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

<!-- Role Field -->
<user-role-and-builder-fields
  role-label="{{ __('user.labels.role') }}"
  :roles="{{ $roles }}"
  :default-role="{{ $userRole ?? 'null' }}"
  builder-label="{{ __('user.labels.builder') }}"
  :default-builder="{{ $builderId ?? 'null' }}"
></user-role-and-builder-fields>

<!-- Password Field -->
<div class="form-group col-md-6">
  {!! Form::label('password', __('user.labels.password')) !!}
  {!! Form::password('password', ['class' => 'form-control']) !!}
</div>

<!-- Confirmation Password Field -->
<div class="form-group col-md-6">
  {!! Form::label('password_confirmation', __('user.labels.password_confirmation')) !!}
  {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
  {!! Form::submit(__('user.labels.btn_submit'), ['class' => 'btn btn-primary']) !!}
  <a href="{!! route('users.index') !!}" class="btn btn-default">
    {{ __('user.labels.btn_cancel') }}
  </a>
</div>
