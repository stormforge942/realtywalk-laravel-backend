<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Polygon;

class UpdatePolygonRequest extends FormRequest
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
        return Polygon::$rules + [
            'color' => ($this->zoom == 2 ? '' : 'required|').'max:7',
        ];
    }

    /**
     * Modify attribute labels
     */
    public function attributes()
    {
        return [
            'zone_id' => 'zone'
        ];
    }
}
