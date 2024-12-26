<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Property;

class CreatePropertyRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Property::$rules;
    }

    /**
     * Modify attribute labels
     */
    public function attributes()
    {
        return [
            'builder_id' => 'builder',
            'category_id' => 'category'
        ];
    }

    /**
     * Customize message for attribute rule
     */
    public function messages()
    {
        return [
            'builder_id.integer' => 'Please select a builder',
            'category_id.integer' => 'Please select a category'
        ];
    }
}
