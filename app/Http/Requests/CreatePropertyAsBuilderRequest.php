<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePropertyAsBuilderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRole('builder');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'          => 'required|min:2|max:100',
            'mls_number'     => 'required|min:2|max:10',
            'price_from'     => 'required|regex:/^\d+(,\d+)*$/',
            'price_to'       => 'required|regex:/^\d+(,\d+)*$/',
            'address_number' => 'nullable|min:3|max:50',
            'address_street' => 'nullable|min:3|max:250',
            'unit_number'    => 'nullable',
            'zipcode'        => 'required|min:2|string|max:10',
            'lat'            => 'nullable|numeric',
            'lng'            => 'nullable|numeric',
            'category_id'    => 'required|integer',
            'bedrooms'       => 'nullable|integer',
            'bathrooms_full' => 'nullable|integer',
            'bathrooms_half' => 'nullable|integer',
            'descr'          => 'nullable|min:10|string',
            'year_built'     => 'required|date_format:Y',
            'lot_size'       => 'required|numeric',
            'status'         => 'required|string|max:40',
            'sqft'           => 'nullable|regex:/^\d+(,\d+)*$/',
            'video_embed'    => 'nullable|max:500',
            'gallery.*'      => 'mimes:jpeg,bmp,png,webp|max:10000'
        ];
    }
}
