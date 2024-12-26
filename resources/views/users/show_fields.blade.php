<!-- Name Field -->
<div class="form-group">
  <b>{!! Form::label('name', __('user.labels.name')) !!}</b>
  <p>{!! $user->name !!}</p>
</div>

<!-- Email Field -->
<div class="form-group">
  <b>{!! Form::label('email', __('user.labels.email')) !!}</b>
  <p>{!! $user->email !!}</p>
</div>

<!-- Role -->
<div class="form-group">
  <b>{!! Form::label('role', __('user.labels.role')) !!}</b>
  <p>{{ $userRole }}</p>
</div>

<!-- Registered At Field -->
<div class="form-group">
  <b>{!! Form::label('registered_at', __('user.labels.registered_at')) !!}</b>
  <p>{!! $user->created_at->toDayDateTimeString() !!}</p>
</div>

<!-- Last Login At Field -->
<div class="form-group">
  <b>{!! Form::label('last_login_at', __('user.labels.last_login_at')) !!}</b>
  <p>{!! $user->last_login_at ? $user->last_login_at->toDayDateTimeString() : '-' !!}</p>
</div>
