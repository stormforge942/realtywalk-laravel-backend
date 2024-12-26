{{-- Email Field --}}
@php
    $notif_email = (array) $settings->getBy('secretary_notification_email');
@endphp

<div class="row">
    <div id="secretary-emails-container" class="form-group">
        {!! Form::hidden('type', 'secretary') !!}
        @if ($notif_email)
            {!! Form::label('secretary_notification_email(s)', '') !!}
            <div>
                @foreach ($notif_email as $email)
                    <div class="inner-emails">
                        {!! Form::email('secretary_notification_email[]', $email, ['class' => 'form-control']) !!}
                        @if (!$loop->first)
                            <button type='button' class='btn btn-default remove'>
                                {{ __('setting.email.btn_remove') }}
                            </button>
                        @endif
                        @if ($loop->first)
                            <button id="add-secretary-email" type="button" class="btn btn-primary add-email">
                                {{ __('setting.email.btn_add') }}
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            {!! Form::label('secretary_notification_email(s)', '') !!}
            <div class="inner-emails">
                {!! Form::email('secretary_notification_email[]', '', ['class' => 'form-control']) !!}

                <button id="add-secretary-email" type="button" class="btn btn-primary add-email">
                    {{ __('setting.email.btn_add') }}
                </button>
            </div>
        @endif
    </div>

    <!-- Submit Field -->
    <div class="row">
        <div class="form-group col-sm-12">
            {!! Form::submit(__('setting.email.btn_submit'), ['class' => 'btn btn-primary']) !!}
            <a href="{!! url()->current() !!}" class="btn btn-default">
                {{ __('setting.email.btn_cancel') }}
            </a>
        </div>
    </div>
</div>
