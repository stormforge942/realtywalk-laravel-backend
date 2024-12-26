<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Builder;

class UpdateBuilderRequest extends FormRequest
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
        $id = $this->route('builder');

        $rules = [
            'name'     => 'required|min:2|max:50',
            // 'slug'     => 'nullable|max:50|unique:builders,slug,'.$id,
            'slug'     => 'nullable|max:50',
            'descr'    => 'nullable|min:5|max:1000',
            'email'    => 'nullable|email|max:128',
            'address1' => 'nullable|min:4|max:128',
            'address2' => 'nullable|min:4|max:128',
            'address3' => 'nullable|min:4|max:128',
            'city'     => 'nullable|min:2|max:128',
            'phone'    => 'nullable|max:12',
            'website'  => 'nullable|url|max:128',
        ];

        return $rules;
    }


    /**
     * Modify attribute labels
     */
    public function attributes()
    {
        return [
            'website' => 'builder website'
        ];
    }
}
