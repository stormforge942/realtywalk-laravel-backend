<!-- Name Field -->
<div class="form-group col-md-6">
    <label>{{ __('property_status.labels.name') }}</label>
    @if(isset($status))
    <input class="form-control" name="name" maxlength="150" value="{{$status->name}}" />
    @else
<input class="form-control" name="name" maxlength="150" value="{{old('name')}}" />
    @endif
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" class="btn btn-primary">
        {{ __('property_status.labels.btn_submit') }}
    </button>
    <a href="{{ route('status.index') }}" class="btn btn-default">
        {{ __('property_status.labels.btn_cancel') }}
    </a>
</div>
