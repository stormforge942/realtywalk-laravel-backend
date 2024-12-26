<div class="form-group">
  <h3>{{ __('property.labels.details') }}</h3>
</div>

@if(count($property->getMedia('properties')) > 0)
  <div class="db--image__upload--container">
    @foreach ($property->getMedia('properties') as $image)
        <div class="db--image__upload">
            <img src="{{ $image->getFullUrl() }}" class="img-fluid" height="100" />
        </div>
    @endforeach
  </div>
@endif

<!-- Title Field -->
<div class="form-group">
  <b>{!! Form::label('title', __('property.labels.title')) !!}</b>
  <p>{{ $property->title }}</p>
</div>

<!-- MLS Number Field -->
<div class="form-group">
  <b>{!! Form::label('mls_number', __('property.labels.mls_number')) !!}</b>
  <p>{{ $property->mls_number }}</p>
</div>

<!-- Price Field -->
<div class="form-group">
  <b>{!! Form::label('price', __('property.labels.price')) !!}</b>
  <p>
    @if($property->price_format_id == 1)
      ${{ number_format($property->price_from, 2) }}
    @elseif($property->price_format_id == 2)
      ${{ number_format($property->price_from, 2) }} - ${{ number_format($property->price_to, 2) }}
    @elseif($property->price_format_id == 3)
      {{ __('general.text_tbd') }}
    @endif
  </p>
</div>

<!-- Status Field -->
<div class="form-group">
  <b>{!! Form::label('status', __('property.labels.status')) !!}</b>
  <p>{{ $property->status }}</p>
</div>

<!-- Type Field -->
<div class="form-group">
  <b>{!! Form::label('type', __('property.labels.type')) !!}</b>
  <p>{{ $property->type == 0 ? __('property.labels.type_listing') : __('property.labels.type_posting') }}</p>
</div>

@if($property->type == 0)
<div class="form-group">
  <b>{!! Form::label('agent', __('property.labels.agent')) !!}</b>
  <p>{{ $property->agent }}</p>
</div>
@endif

<!-- Category Field -->
<div class="form-group">
  <b>{!! Form::label('category', __('property.labels.category')) !!}</b>
  <p>{{ $property->category ? $property->category->name : '-' }}</p>
</div>

<!-- Year Built Field -->
<div class="form-group">
  <b>{!! Form::label('year_built', __('property.labels.year_built')) !!}</b>
  <p>{{ $property->year_built }}</p>
</div>

<!-- Bedrooms Field -->
<div class="form-group">
  <b>{!! Form::label('bedrooms', __('property.labels.bedrooms')) !!}</b>
  <p>{{ $property->bedrooms ?: 0 }}</p>
</div>

<!-- Bedrooms Full Field -->
<div class="form-group">
  <b>{!! Form::label('bathrooms_full', __('property.labels.full_bathrooms')) !!}</b>
  <p>{{ $property->bathrooms_full ?: 0 }}</p>
</div>

<!-- Bedrooms Half Field -->
<div class="form-group">
  <b>{!! Form::label('bathrooms_half', __('property.labels.half_bathrooms')) !!}</b>
  <p>{{ $property->bathrooms_half ?: 0 }}</p>
</div>

<!-- Builder Field -->
<div class="form-group">
  <b>{!! Form::label('builder', __('property.labels.builder')) !!}</b>
  <p>{{ $property->builder ? $property->builder->name : '-' }}</p>
</div>

<!-- Style Field -->
<div class="form-group">
  <b>{!! Form::label('styles', __('property.labels.styles')) !!}</b>

  @if($property->styles->count())
  <ul>
    @foreach($property->styles as $style)
    <li>{{ $style->name }}</li>
    @endforeach
  </ul>
  @else
  <p>-</p>
  @endif
</div>

<!-- Lot Size Field -->
<div class="form-group">
  <b>{!! Form::label('lot_size', __('property.labels.lot_size')) !!}</b>
  <p>{{ $property->lot_size ?: '-' }} acres</p>
</div>

<div class="form-group">
  <h3>{{ __('property.labels.amenities') }}</h3>
</div>

@if($property->amenities->count())
  <ul>
    @foreach ($property->amenities as $amenity)
    <li>{{ $amenity->name }}</li>
    @endforeach
  </ul>
@else
  <p>{{__('property.labels.amenities_no_data')  }}</p>
@endif

<div class="form-group">
  <h3>
    {{ __('property.labels.geo_info') }}
  </h3>
</div>

<!-- Area Field -->
@php
$area = '-';
$areacrumbs = [];
$zonecrumbs = [];

if($property->polygon_id) {
  $ancestors = $property->polygon->ancestors;
  if ($ancestors->count()) {
    foreach ($ancestors as $node) {
      $areacrumbs[] = $node->title;
    }
  }

  $areacrumbs[] = $property->polygon->title;

  $zone = $property->polygon->zone;

  if ($zone->ancestors->count()) {
    foreach ($zone->ancestors as $node) {
      $zonecrumbs[] = $node->name;
    }
  }

  $zonecrumbs[] = $zone->name;

  $area = implode(' &rsaquo; ', array_merge($zonecrumbs, $areacrumbs));
}
@endphp
<div class="form-group">
  <b>{!! Form::label('area', __('property.labels.area')) !!}</b>
  <p>{!! $area !!}</p>
</div>

<!-- Street Number Field -->
<div class="form-group">
  <b>{!! Form::label('address_number', __('property.labels.address_number')) !!}</b>
  <p>{{ $property->address_number }}</p>
</div>

<!-- Street Name Field -->
<div class="form-group">
  <b>{!! Form::label('address_street', __('property.labels.address_street')) !!}</b>
  <p>{{ $property->address_street }}</p>
</div>

<div class="form-group">
  <b>{!! Form::label('unit_number', __('property.labels.unit_number')) !!}</b>
  <p>{{ $property->unit_number }}</p>
</div>

<!-- Zipcode Field -->
<div class="form-group">
  <b>{!! Form::label('zipcode', __('property.labels.zipcode')) !!}</b>
  <p>{{ $property->zipcode }}</p>
</div>

<!-- Lat Field -->
<div class="form-group">
  <b>{!! Form::label('lat', __('property.labels.lat')) !!}</b>
  <p>{{ !is_null($property->lat) ? $property->lat : '-' }}&deg;</p>
</div>

<!-- Lng Field -->
<div class="form-group">
  <b>{!! Form::label('lng', __('property.labels.lng')) !!}</b>
  <p>{{ !is_null($property->lng) ? $property->lng : '-' }}&deg;</p>
</div>

<div class="form-group">
  <h3>
    {{ __('property.labels.other') }}
  </h3>
</div>

<!-- Video Embed Field -->
@if($embed_vid = $property->video_embed)
<div class="form-group">
  <b>{!! Form::label('video_embed', __('property.labels.video_embed')) !!}</b>
  <p>{!! $embed_vid !!}</p>
</div>
@endif

<!-- Descr Field -->
<div class="form-group">
  <b>{!! Form::label('descr', __('property.labels.descr')) !!}</b>
  <p>{{ $property->descr }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
  <b>{!! Form::label('created_at', __('property.labels.created_at')) !!}</b>
  <p>{{ $property->created_at->toDayDateTimeString() }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
  <b>{!! Form::label('updated_at', __('property.labels.updated_at')) !!}</b>
  <p>{{ $property->updated_at->toDayDateTimeString() }}</p>
</div>
