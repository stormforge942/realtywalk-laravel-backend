<div class="form-group col-12">
  <h3>
    {{ __('polygon.labels.featured_image') }}
  </h3>
</div>

<!-- Featured Image Field -->
<div class="form-group col-sm-6 featured-image-file-input single-file"{!! isset($polygon) && $polygon->is_uploading_files ? ' style="cursor: not-allowed;"' : '' !!}>
  <file-pond
    ref="featured_image"
    :disabled="{{ isset($polygon) && $polygon->is_uploading_files ? 'true' : 'false' }}"
    label-idle='Drag &amp; drop your file here or <span class="filepond--label-action"> Browse </span>'
    :allow-multiple="false"
    max-file-size="10MB"
    accepted-file-types="image/jpeg, image/png"
    :files={{ !empty($featured_image) ? @json_encode([$featured_image]) : json_encode('') }}
    @removefile="addDeletedFeaturedImagesToList"
  />
</div>

<div class="form-group col-12">
  <h3>
    {{ __('polygon.labels.gallery') }}
  </h3>
</div>

<!-- Gallery Field -->
<div class="form-group col-12 gallery-file-input"{!! isset($polygon) && $polygon->is_uploading_files ? ' style="cursor: not-allowed;"' : '' !!}>
  <file-pond
    ref="gallery"
    :disabled="{{ isset($polygon) && $polygon->is_uploading_files ? 'true' : 'false' }}"
    label-idle='Drag &amp; drop your files here or <span class="filepond--label-action"> Browse </span>'
    allow-multiple="true"
    allow-reorder="true"
    max-file-size="10MB"
    accepted-file-types="image/jpeg, image/png"
    style-item-panel-aspect-ratio=".56"
    :files={{ isset($gallery) ? json_encode($gallery) : json_encode("")}}
    @removefile="addDeletedFilesToList"
  />
</div>

<!-- Title Field -->
<div class="form-group col-md-6">
  {!! Form::label('title', __('polygon.labels.title')) !!}
  {!! Form::text('title', null, ['class' => 'form-control','maxlength' => 100]) !!}
</div>

<div class="col-md-12 remove-lats">
  <poly-map :initial-geo-json='{{ $polygon->geometry ?? 'null' }}' :polygon-id='{{ $polygon->id ?? 'null' }}' :initial-zoom='{{ $polygon->zoom ?? 'null' }}' show-neighbors="true" full-screen disable-links></poly-map>
</div>

<!-- Tree Select Polygon -->
<tree-select-polygon fetch-zone-url="{{ route('dtr.zones') }}"
  :selected-zone={{ isset($polygon) && $polygon->zone_id ? $polygon->zone_id : 'null' }}
  fetch-parent-url="{{ route('dtr.polygons') }}"
  :selected-parent={{ isset($polygon) && $polygon->parent_id ? $polygon->parent_id : 'null' }}
></tree-select-polygon>
<div class="alert alert-warning mx-3">
  {{ __('polygon.labels.zone_help') }}
</div>

<!-- Color Field -->
<div class="col-sm-12 col-md-6">
  <div classs="form-group">
    {!! Form::label('color', __('polygon.labels.color')) !!}
    <span class="color-polygon-info" style="display:none;">inherited from parent polygon</span>

    <color-picker id="polygonColor" name="color" color="{{ $polygon->color ?? '#000000'  }}" />
  </div>
</div>

<!-- Zoom Field -->
<div class="form-group col-sm-6 col-md-3 pt-3">
  {!! Form::label('zoom', __('polygon.labels.zoom')) !!}
  <select name="zoom" id="zoom" class="form-control">
    @if(Route::currentRouteName() == 'polygons.edit')
    <option {{$polygon->zoom == 1? 'selected':''}} value="1">
      {{ __('polygon.labels.level_n', ['n' => 1]) }}
    </option>
    <option {{$polygon->zoom == 2? 'selected':''}} value="2">
      {{ __('polygon.labels.level_n', ['n' => 2]) }}
    </option>
    <option {{$polygon->zoom == 3? 'selected':''}} value="3">
      {{ __('polygon.labels.level_n', ['n' => 3]) }}
    </option>
    @else
    <option {{old('zoom') == 1 ? 'selected':''}} value="1">
      {{ __('polygon.labels.level_n', ['n' => 1]) }}
    </option>
    <option {{old('zoom') == 2 ? 'selected':''}} value="2">
      {{ __('polygon.labels.level_n', ['n' => 2]) }}
    </option>
    <option {{old('zoom') == 3 ? 'selected':''}} value="3">
      {{ __('polygon.labels.level_n', ['n' => 3]) }}
    </option>
    @endif
  </select>
</div>

<!-- Display as background -->
<div class="form-group col-sm-12 col-md-6">
  <input type="checkbox" name="display_as_background" id="display_as_background"
      {{isset($polygon) && $polygon->display_as_background ? "checked='checked'": ""}}/>
  {!! Form::label('display_as_background', __('polygon.labels.display_as_background')) !!}
</div>

<!-- Previously added links -->
@if($options['link_list'] ?? false)
  <div id="edit-links-container">
    <p>
      {{ __('polygon.labels.link_list') }}
    </p>
    @foreach($options['link_list'] as $link)
      <div class="added-link">
        {!! Form::text('links[label][]', $link['label'], ['class' => 'form-control', 'placeholder' => 'Label']) !!}
        {!! Form::text('links[url][]', $link['url'], ['class' => 'form-control ml-2', 'placeholder' => 'URL (https://)']) !!}
        <button type='button' class='btn btn-default remove'>
          {{ __('polygon.labels.btn_remove_link') }}
        </button>
      </div>
    @endforeach
    <button id="add" type="button" class="btn btn-primary">
      {{ __('polygon.labels.btn_add_link') }}
    </button>
  </div>
@endif

<!-- Links field -->
<div id="links-container" class="form-group">
  {!! Form::label('Links', '') !!}
  <div class="links">
    {!! Form::text('links[label][]', '', ['class' => 'form-control', 'placeholder' => 'Label']) !!}
    {!! Form::text('links[url][]', '', ['class' => 'form-control ml-2', 'placeholder' => 'URL (https://)']) !!}

    <button id="add" type="button" class="btn btn-primary">
      {{ __('polygon.labels.btn_add_link') }}
    </button>
  </div>
</div>

<!-- Editor -->
<div class="desc-editor-container">
  {!! Form::label('description', __('polygon.labels.description')) !!}
  {!!Form::textarea("description", null, ["class" => "desc-editor"]) !!}
</div>

<!-- Statistic Fields -->
@php
$statistics = null;

if (isset($polygon) && $polygon->statistics->count()) {
  $statistics = $polygon->statistics->pluck('pivot');
}
@endphp
<div class="form-group col-12 mt-3">
  <h3>{{ __('polygon.labels.statistics') }}</h3>
</div>

@if($statisticTypes)
<ol class="pl-3">
  @foreach($statisticTypes as $type)
  <li>
    <h5>{{ $type->name }}</h5>

    @if($type->statistics->count())
    @foreach($type->statistics->chunk(2) as $row)
    <div class="row mb-3">
      @foreach($row as $stat)
      <div class="col-6 col-sm-4 col-md-3">
        {!! Form::label('statistics['.$stat->id.'][value]', $stat->name) !!}

        <input-mask name="statistics[{{ $stat->id }}][value]" value="{{
          $statistics
            ? ($statistics->firstWhere('statistic_id', $stat->id)->value ?? '') : ''
          }}" mask="{{ get_inputmask($type->format) }}" />
      </div>
      @endforeach
    </div>
    @endforeach
    @else
    {{ __('polygon.labels.statistics_no_data_under_current_type') }}
    @endif
  </li>
  @endforeach
</ol>
@else
<p>{{ __('polygon.labels.statistic_type_no_data') }}</p>
@endif

{{-- Submit Field --}}
<div class="form-group col-sm-12">
  {!! Form::submit(__('polygon.labels.btn_submit'), ['class' => 'btn btn-primary']) !!}

  <a href="{{ route('polygons.index') }}" class="btn btn-default">
  {{ __('polygon.labels.btn_cancel') }}
  </a>
</div>
