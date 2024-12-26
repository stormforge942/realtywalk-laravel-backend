@php
$link = url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset());
@endphp

{!! __('passwords.mail.reset_password.body', [
  'url' => '<a href="'.$link.'">'.$link.'</a>'
]) !!}
