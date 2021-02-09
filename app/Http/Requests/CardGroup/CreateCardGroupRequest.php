<?php

namespace App\Http\Requests\CardGroup;

use Illuminate\Foundation\Http\FormRequest;

class CreateCardGroupRequest extends FormRequest
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
            'message' => 'required|exists:messages,uuid',
            'position' => 'required|integer',
            'height' => 'required'
        ];
    }
}
