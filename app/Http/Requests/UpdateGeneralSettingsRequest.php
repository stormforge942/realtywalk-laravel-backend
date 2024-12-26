<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingsRequest extends FormRequest
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
        return [
            'site_title'          => 'required|max:50',
            'site_favicon'        => 'image|mimes:jpeg,png,ico',
            'site_logo_expanded'  => 'image|mimes:jpeg,gif,png,svg|max:1024',
            'site_logo_collapsed' => 'image|mimes:jpeg,gif,png,svg|max:1024',
            'terms_of_service'    => 'max:30000',
            'privacy_policy'      => 'max:30000',
        ];
    }
}
