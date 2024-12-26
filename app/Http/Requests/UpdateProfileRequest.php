<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
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
        $id = Auth::user()->id;

        return [
            'name'     => 'required',
            'picture'  => 'image|mimes:jpeg,gif,png|max:1024',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'confirmed',
        ];
    }
}
