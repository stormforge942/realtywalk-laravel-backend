<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
        $rules = [
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'nullable|confirmed',
            'role'       => 'required',
            'builder_id' => 'required_if:role,builder'
        ];

        return $rules;
    }
}
