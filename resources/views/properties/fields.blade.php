<div class="form-group col-12">
  <h3>
    {{ __('property.labels.gallery') }}
  </h3>
</div>

<!-- Gallery Field -->
<div class="form-group col-12 gallery-file-input"{!! isset($property) && $property->is_uploading_files ? ' style="cursor: not-allowed;"' : '' !!}>
  <file-pond
    ref="gallery"
    :disabled="{{ isset($property) && $property->is_uploading_files ? 'true' : 'false' }}"
    label-idle='Drag &amp; drop your files here or <span class="filepond--label-action"> Browse </span>'
    allow-multiple="true"
    allow-reorder="true"
    max-file-size="10MB"
    style-item-panel-aspect-ratio=".56"
    accepted-file-types="image/jpeg, image/png"
    :files={{ isset($gallery) ? json_encode($gallery) : json_encode("")}}
    @removefile="addDeletedFilesToList"
  />
</div>

<div class="form-group col-12">
  <h3>{{ __('property.labels.details') }}</h3>
</div>

<!-- Title Field -->
<div class="form-group col-md-6">
  {!! Form::label('title', __('property.labels.title')) !!}
  {!! Form::text('title', null, ['class' => 'form-control','maxlength' => 100]) !!}
</div>

<!-- MLS Number Field -->
<div class="form-group col-sm-4 col-md-3">
  {!! Form::label('mls_number', __('property.labels.mls_number')) !!}
  {!! Form::text('mls_number', null, [
        'v-input-mask', 'class' => 'form-control',
        'data-inputmask' => "
          'mask': '9',
          'repeat': 10,
          'greedy': false
        ",
      ])
  !!}
</div>

<slider-picker
  label="{{ __('property.labels.price') }}"
  name="price"
  money="true"
  step="25000"
  :formats={{$priceFormats}}
  default-from="{{ isset($property) ? $property->price_from : 25000 }}"
  default-to="{{ isset($property) && isset($property->price_to) ? $property->price_to : null }}"
  default-format="{{ isset($property) ? $property->price_format_id : 1 }}">
</slider-picker>

<property-listing-fields defaulttype="{{$property->type ?? 1}}" defaultagent="{{$property->agent ?? ''}}"></property-listing-fields>

<div class="form-group col-sm-12 col-md-4">
  <label>
    {{ __('property.labels.status') }}
  </label>
  <select name="status" class="form-control">
    @foreach($propertiesStatus as $status)
    @if(isset($property->status))
    <option {{$property->status === $status->name? 'selected':false}} value="{{$status->name}}">{{$status->name}}</option>
    @else
    <option value="{{$status->name}}">{{$status->name}}</option>
    @endif
    @endforeach
  </select>
</div>


<!-- Category Field -->
<div class="form-group col-md-6">
  {!! Form::label('category_id', __('property.labels.category')) !!}

  <tree-select
    fetch-url="{{ route('list.categories') }}"
    name="category_id"
    placeholder="{{ __('property.labels.category_placeholder') }}"
    :selected-options="{{ isset($parent) && $parent->category_id ? $parent->category_id : 'null' }}"
  />
</div>

<!-- Year Built Field -->
<div class="form-group col-sm-4 col-md-3">
  {!! Form::label('year_built', __('property.labels.year_built')) !!}
  {!! Form::text('year_built', null, [
        'v-input-mask', 'class' => 'form-control',
        'data-inputmask' => "
          'alias'          : 'datetime',
          'inputFormat'    : 'yyyy',
          'placeholder'    : '____',
          'clearIncomplete': true,
          'min'            : '1900',
          'max'            : '".now()->format('Y')."'
        ",
      ])
  !!}
</div>

<div class="form-group col-md-9">
  <div class="row">
    <!-- Bedrooms Field -->
    <div class="col-sm-4 pl-0">
      {!! Form::label('bedrooms', __('property.labels.bedrooms')) !!}
      {!! Form::text('bedrooms', null, [
            'v-input-mask', 'class' => 'form-control',
            'data-inputmask' => "
              'mask': '9',
              'repeat': 2,
              'greedy': false
            ",
          ])
      !!}
    </div>

    <!-- Bathrooms (Full) Field -->
    <div class="col-sm-4 pl-0">
      {!! Form::label('bathrooms_full', __('property.labels.full_bathrooms')) !!}
      {!! Form::text('bathrooms_full', null, [
            'v-input-mask', 'class' => 'form-control',
            'data-inputmask' => "
              'mask': '9',
              'repeat': 2,
              'greedy': false
            ",
          ])
      !!}
    </div>

    <!-- Bathrooms (Half) Field -->
    <div class="col-sm-4 px-0">
      {!! Form::label('bathrooms_half', __('property.labels.half_bathrooms')) !!}
      {!! Form::text('bathrooms_half', null, [
            'v-input-mask', 'class' => 'form-control',
            'data-inputmask' => "
              'mask': '9',
              'repeat': 2,
              'greedy': false
            ",
          ])
      !!}
    </div>
  </div>
</div>

<!-- Builder Field -->
<div class="form-group col-md-6">
  {!! Form::label('builder_id', __('property.labels.builder')) !!}

  <builder-select
    fetch-url="{{ route('list.builders') }}"
    name="builder_id"
    :selected-option="{{ isset($property) && $property->builder_id ? $property->builder_id : 'null' }}"
  />
</div>

<!-- Style Field -->
@php
if (isset($property) && $property->styles->count()) {
  $selected_styles = implode(',', $property->styles->pluck('id')->all());
}
@endphp

<div class="form-group col-md-6">
  {!! Form::label('styles_id', __('property.labels.styles')) !!}

  <tree-select
    fetch-url="{{ route('list.styles') }}"
    name="styles_id[]"
    placeholder="{{ __('property.labels.styles_placeholder') }}"
    :multiple="true"
    :selected-options="{{ isset($selected_styles) && $selected_styles ? '['.$selected_styles.']' : 'null' }}"
  />
</div>

<div class="form-group col-10">
  <slider-combo label="{{ __('property.labels.sqft') }}" name="sqft" money="false" step="10" default="{{ isset($property) ? $property->sqft : 1000 }}"></slider-combo>
</div>

<!-- Lot Size Field -->
<div class="form-group col-sm-4 col-md-3">
  {!! Form::label('lot_size', __('property.labels.lot_size')) !!}

  <input-mask
    name="lot_size"
    value="{{ isset($property) ? $property->lot_size : '' }}"
    mask="
      'alias'             : 'numeric',
      'digits'            : 2,
      'digitsOptional'    : true,
      'suffix'            : ' acres',
      'autoGroup'         : true,
      'groupSeparator'    : ',',
      'autoUnmask'        : true,
      'removeMaskOnSubmit': true,
      'placeholder'       : '0'
    "
  />
</div>

<!-- Lot Dimension Fields -->
<div class="row">
    <div class="form-group col-sm-6">
        <slider-combo label="{{ __('property.labels.lot_front_dimension') }}" name="lot_front_dimension" money="false" step="1" max="25000000" default="{{ isset($property) ? $property->lot_front_dimension : 1 }}"></slider-combo>
    </div>
    <div class="form-group col-sm-6">
        <slider-combo label="{{ __('property.labels.lot_back_dimension') }}" name="lot_back_dimension" money="false" step="1" max="25000000" default="{{ isset($property) ? $property->lot_back_dimension : 1 }}"></slider-combo>
    </div>
    <div class="form-group col-sm-6">
        <slider-combo label="{{ __('property.labels.lot_left_dimension') }}" name="lot_left_dimension" money="false" step="1" max="25000000" default="{{ isset($property) ? $property->lot_left_dimension : 1 }}"></slider-combo>
    </div>
    <div class="form-group col-sm-6">
        <slider-combo label="{{ __('property.labels.lot_right_dimension') }}" name="lot_right_dimension" money="false" step="1" max="25000000" default="{{ isset($property) ? $property->lot_right_dimension : 1 }}"></slider-combo>
    </div>
</div>

<div class="form-group col-sm-4 col-md-3">
  {!! Form::label('stories', __('property.labels.stories')) !!}
  {!! Form::text('stories', null, [
      'v-input-mask', 'class' => 'form-control',
      'data-inputmask' => "
        'mask': '9',
        'repeat': 2,
        'greedy': false
      ",
    ])
  !!}
</div>

<div class="form-group col-12">
  <h3>{{ __('property.labels.amenities') }}</h3>
</div>

@php
$selected_amenities = $property->amenities->pluck('id')->toArray();
@endphp

<div class="form-group col-md-6">
  @foreach($amenities->chunk(2) as $row)
  <div class="row">
    @foreach($row as $item)
      <div class="col-sm-6">
        <div class="form-check checkbox">
          <input class="form-check-input" id="amenity-{{ $item->id }}" type="checkbox" name="amenities_id[]" value="{{ $item->id }}"{{ in_array($item->id, $selected_amenities) ? ' checked' : '' }}>
          <label class="form-check-label" for="amenity-{{ $item->id }}">
            {{ $item->name }}
          </label>
        </div>
      </div>
    @endforeach
  </div>
  @endforeach
</div>

<div class="form-group col-12">
  <h3>{{ __('property.labels.geo_info') }}</h3>
</div>

<!-- Area Field -->
<div class="form-group col-md-6">
  {!! Form::label('polygon_id', __('property.labels.polygon')) !!}

  <tree-select-polygon-areas
    fetch-url="{{ route('dtr.builders.areas') }}"
    name="polygon_id"
    placeholder="{{ __('property.labels.polygon_placeholder') }}"
    :selected-options="{{ isset($property) && $property->polygon_id ? $property->polygon_id : 'null' }}"
    :disabled="true"
  />
</div>

<!-- Street Number Field -->
<div class="form-group col-sm-4 col-md-3">
  {!! Form::label('address_number', __('property.labels.address_number')) !!}
  {!! Form::text('address_number', null, ['class' => 'form-control','maxlength' => 50]) !!}
</div>

<!-- Street Name Field -->
<div class="form-group col-md-9">
  {!! Form::label('address_street', __('property.labels.address_street')) !!}
  {!! Form::text('address_street', null, ['class' => 'form-control','maxlength' => 100]) !!}
</div>

<div class="form-group col-md-9">
  {!! Form::label('unit_number', __('property.labels.unit_number')) !!}
  {!! Form::text('unit_number', null, ['class' => 'form-control','maxlength' => 100]) !!}
</div>

<!-- Zipcode Field -->
<div class="form-group col-sm-4 col-md-3">
  {!! Form::label('zipcode', __('property.labels.zipcode')) !!}

  {!! Form::text('zipcode', null, [
        'v-input-mask', 'class' => 'form-control',
        'data-inputmask' => "
          'mask': '99999',
          'clearIncomplete': true
        ",
      ])
  !!}
</div>

<geo-picker defaultlat="{{ isset($property) && $property->lat ? $property->lat : '' }}" defaultlng="{{ isset($property) && $property->lng ? $property->lng : '' }}" input-row-class="form-group col-md-6" map-row-class="col-12">
</geo-picker>

<div class="form-group col-12">
  <h3>
    {{ __('property.labels.other') }}
  </h3>
</div>

<!-- Video Embed Field -->
<!--form-control  -->
<div class="form-group col-md-9">
  {!! Form::label('video_embed', __('property.labels.video_embed')) !!}
  {!! Form::textarea('video_embed', null, ['class' => 'html-editor','maxlength' => 500]) !!}
</div>

<!-- Description Field -->
<div class="form-group col-md-9">
  {!! Form::label('descr', __('property.labels.descr')) !!}
  {!!Form::textarea("descr", null, ["class" => "desc-editor form-control" ]) !!}
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12">
  {!! Form::submit(__('property.labels.btn_submit'), ['class' => 'btn btn-primary', 'id' => 'propertySubmit']) !!}
  <a href="{{ route('properties.index') }}" class="btn btn-default">
    {{ __('property.labels.btn_cancel') }}
  </a>
</div>
