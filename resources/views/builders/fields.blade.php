<!-- Name Field -->
<div class="row mt-4">
  <div class="form-group col-md-8 mt-4">
    <div>
      <h3>{{ __('builder.labels.gallery') }}</h3>
    </div>
    <!-- Gallery Field -->
    <div class="gallery-file-input"{!! isset($builder) && $builder->is_uploading_files ? ' style="cursor: not-allowed;"' : '' !!}>
      <file-pond
        ref="gallery"
        :disabled="{{ isset($builder) && $builder->is_uploading_files ? 'true' : 'false' }}"
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
  </div>
  <div class="form-group col-md-4 mt-4">
    <div class="logo-file-input"{!! isset($builder) && $builder->is_uploading_files ? ' style="cursor: not-allowed;"' : '' !!}>
      <h3>{{ __('builder.labels.logo') }}</h3>
      <file-pond
        ref="logo"
        :disabled="{{ isset($builder) && $builder->is_uploading_files ? 'true' : 'false' }}"
        label-idle='Drag &amp; drop your files here or <span class="filepond--label-action"> Browse </span>'
        :allow-multiple="false"
        max-file-size="10MB"
        accepted-file-types="image/jpeg, image/png"
        style-item-panel-aspect-ratio=".65"
        :files={{ isset($logo) ? json_encode($logo) : json_encode("")}}
        @removefile="addDeletedLogosToList"
      />
    </div>
  </div>
</div>

<div class="form-group col-md-6">
  {!! Form::label('name', __('builder.labels.name')) !!}
  {!! Form::text('name', null, ['class' => 'form-control','maxlength' => 50]) !!}
</div>

<!-- Slug Field -->
<div class="form-group col-md-6">
  {!! Form::label('slug', __('builder.labels.slug')) !!}
  {!! Form::text('slug', null, [
  'v-input-mask', 'class' => 'form-control',
  'data-inputmask' => "
  'regex': '[a-z0-9-]*'
  ",
  'maxlength' => 50]) !!}
  <p class="text-muted">
    {{ __('builder.labels.slug_help') }}
  </p>
</div>

<div class="form-group col-md-6">
  {!! Form::label('alias_of', __('builder.labels.alias_of')) !!}
  <tree-select-builder-alias
    name="alias_of_builder_id"
    :similars="{{ json_encode(collect($similars ?? [])->toArray()) }}"
    builder-id="{{ $builder->id ?? '' }}"
    alias-id="{{ $builder->alias_of_builder_id ?? '' }}"
  />
</div>

<!-- Description Field -->
<div class="form-group col-md-9">
  {!! Form::label('descr', __('builder.labels.descr')) !!}
  {!! Form::textarea("descr", null, ["class" => "desc-editor form-control", 'maxlength' => 1000]) !!}
</div>


<!-- Email Field -->
<div class="form-group col-sm-6 col-md-4">
  {!! Form::label('email', __('builder.labels.email')) !!}

  {!! Form::text('email', null, [
  'v-input-mask', 'class' => 'form-control',
  'data-inputmask' => "
  'alias': 'email',
  'clearIncomplete': true
  ",
  'maxlength' => 128
  ])
  !!}
</div>

<!-- Phone Field -->
<div class="form-group col-sm-6 col-md-4">
  {!! Form::label('phone', __('builder.labels.phone')) !!}

  <input-mask type="tel" name="phone" value="{{ isset($builder) && $builder->phone ? $builder->phone : '' }}" mask="
      'mask'              : '(999) 999-9999',
      'autoUnmask'        : true,
      'clearIncomplete'   : true,
      'removeMaskOnSubmit': true
    " />
</div>

<!-- Website Field -->
<div class="form-group col-sm-6 col-md-4">
  {!! Form::label('website', __('builder.labels.website')) !!}
  {!! Form::url('website', null, [
  'v-input-mask', 'class' => 'form-control',
  'data-inputmask' => "
  'regex': 'http(s?)://.*',
  'greedy': true
  ",
  'maxlength' => 128,
  'placeholder' => 'http(s)://'
  ])
  !!}
</div>

<!-- Builder Areas Field -->
<div class="form-group col-md-6">
  {!! Form::label('builder_areas', __('builder.labels.builder_areas')) !!}

  <tree-select-builder-area fetch-url="{{ route('dtr.builders.areas') }}?lazyload=true" name="builder_areas[]" placeholder="{{ __('builder.labels.builder_areas_placeholder') }}" :multiple="true" :selected-options="{{ isset($builder) && $builder->polygons->count() ? json_encode($builder->polygons->pluck('id')->all()) : 'null' }}" />
</div>

<!-- Address 1 Field -->
<div class="form-group col-md-9">
  {!! Form::label('address1', __('builder.labels.address1')) !!}
  {!! Form::text('address1', null, ['class' => 'form-control','maxlength' => 128]) !!}
</div>

<!-- Address 2 Field -->
<div class="form-group col-md-9">
  {!! Form::label('address2', __('builder.labels.address2')) !!}
  {!! Form::text('address2', null, ['class' => 'form-control','maxlength' => 128]) !!}
</div>

<!-- Address 3 Field -->
<div class="form-group col-md-9">
  {!! Form::label('address3', __('builder.labels.address3')) !!}
  {!! Form::text('address3', null, ['class' => 'form-control','maxlength' => 128]) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
  {!! Form::submit(__('builder.labels.btn_submit'), ['class' => 'btn btn-primary']) !!}
  <a href="{{ route('builders.index') }}" class="btn btn-default">
    {{ __('builder.labels.btn_cancel') }}
  </a>
</div>
