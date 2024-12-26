@component('mail::message')
# Similar Builders

Hello

The following Builders may have similar names.

@component('mail::panel')
@foreach($builders as $builderGroup)
Similar to {{$builderGroup[0]->name}}:
@foreach($builderGroup as $builder)
  - [{{ $builder->name }}]({{url('system/builders/' . $builder->id)}})
@endforeach

@endforeach
@endcomponent

@if($todayAliases->count())
The following builders were aliased today:

@component('mail::panel')
@foreach($todayAliases as $builder)
- [{{ $builder->name }}]({{url('system/builders/' . $builder->id)}}) aliased to [{{ $builder->aliasOf->name }}]({{url('system/builders/' . $builder->id)}})
@endforeach
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}

**This is an automatic email, please do not reply.**
@endcomponent
