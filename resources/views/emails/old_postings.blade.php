@component('mail::message')
# Old Postings

Hello

The following Postings have not been modified for 30 days or more.

@component('mail::panel')
  @foreach($postings as $posting)
  - [{{ $posting->title }}: {{ $posting->full_address }} {{ $posting->zipcode }}]({{url('system/properties/' . $posting->id)}})
  @endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }}

**This is an automatic email, please do not reply.**
@endcomponent
