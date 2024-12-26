@if(isset($builder) && count($builder->getMedia('builder_logo')) > 0)
<div class="db--image__upload--container">
@foreach ($builder->getMedia('builder_logo') as $image)
    <div class="db--image__upload" id="dbImage-{{$image->id}}">
        <img src="{{ $image->getFullUrl() }}" height="100" />
    </div>
@endforeach
</div>
@endif

<div class="form-group">
  <b>{!! Form::label('name', __('builder.labels.name')) !!}</b>
  <p>{{ $builder->name }}</p>
</div>

<!-- Slug Field -->
<div class="form-group">
  <b>{!! Form::label('slug', __('builder.labels.slug')) !!}</b>
  <p>{{ $builder->slug }}</p>
</div>

<!-- Description Field -->
<div class="form-group">
  <b>{!! Form::label('descr', __('builder.labels.descr')) !!}</b>
  <p>{{ $builder->descr ?: '-' }}</p>
</div>

<!-- Email Field -->
<div class="form-group">
  <b>{!! Form::label('email', __('builder.labels.email')) !!}</b>
  <p>
    @if($builder->email)
      <a href="mailto:{{ $builder->email }}">
        {{ $builder->email }}
      </a>
    @else
      -
    @endif
  </p>
</div>

<!-- Address1 Field -->
<div class="form-group">
  <b>{!! Form::label('address1', __('builder.labels.address1')) !!}</b>
  <p>{{ $builder->address1 ?: '-' }}</p>
</div>

<!-- Address2 Field -->
<div class="form-group">
  <b>{!! Form::label('address2', __('builder.labels.address2')) !!}</b>
  <p>{{ $builder->address2 ?: '-' }}</p>
</div>

<!-- Address3 Field -->
<div class="form-group">
  <b>{!! Form::label('address3', __('builder.labels.address3')) !!}</b>
  <p>{{ $builder->address3 ?: '-' }}</p>
</div>

<!-- Phone Field -->
<div class="form-group">
  <b>{!! Form::label('phone', __('builder.labels.phone')) !!}</b>
  <p>{{ $builder->phone ?: '-' }}</p>
</div>

<!-- Website Field -->
<div class="form-group">
  <b>{!! Form::label('website', __('builder.labels.website')) !!}</b>
  <p>
    @if($builder->website)
      <a href="{{ $builder->website }}" target="_blank">
        {{ $builder->website }}

        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" x="0px" y="0px" viewBox="0 0 100 100" width="15" height="15" class="mb-1 text-muted"><path fill="currentColor" d="M18.8,85.1h56l0,0c2.2,0,4-1.8,4-4v-32h-8v28h-48v-48h28v-8h-32l0,0c-2.2,0-4,1.8-4,4v56C14.8,83.3,16.6,85.1,18.8,85.1z"></path> <polygon fill="currentColor" points="45.7,48.7 51.3,54.3 77.2,28.5 77.2,37.2 85.2,37.2 85.2,14.9 62.8,14.9 62.8,22.9 71.5,22.9"></polygon></svg>
      </a>
    @else
      -
    @endif
  </p>
</div>

<div class="form-group">
  <b>{!! Form::label('builder_areas', __('builder.labels.builder_areas')) !!}</b>
  @if($builder->polygons->count())
  <ul class="pl-3">
    @foreach($builder->polygons as $row)
      <li>
        @if($row->ancestors->count())
          {!!
            implode(' &rsaquo; ', $row->ancestors->pluck('title')->all())
          !!}

          &rsaquo;
        @endif

        {{ $row->title }}
      </li>
    @endforeach
  </ul>
  @endif
</div>

<!-- Created At Field -->
<div class="form-group">
  <b>{!! Form::label('created_at', __('builder.labels.created_at')) !!}</b>
  <p>{{ $builder->created_at->toDayDateTimeString() }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
  <b>{!! Form::label('updated_at', __('builder.labels.updated_at')) !!}</b>
  <p>{{ $builder->updated_at->toDayDateTimeString() }}</p>
</div>
