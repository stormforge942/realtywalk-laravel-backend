@if ($position == 'footer')
{!! removeTrailingQuotes(\App\Models\Setting::getBy('footer_scripts')) !!}
@endif

@if ($position == 'header')
{!! removeTrailingQuotes(\App\Models\Setting::getBy('header_scripts')) !!}
@endif
