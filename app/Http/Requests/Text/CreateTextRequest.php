<?php

namespace App\Http\Requests\Text;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Message;

class CreateTextRequest extends FormRequest
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
            'text' => 'required|min:2',
            'message' => 'required|exists:messages,uuid',
            'position' => 'required|integer',
            'height' => 'required'
        ];
    }
}
