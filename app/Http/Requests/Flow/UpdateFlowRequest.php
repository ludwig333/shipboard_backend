<?php

namespace App\Http\Requests\Flow;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Flow;

class UpdateFlowRequest extends FormRequest
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
            'name' => "required|string|min:3|unique:flows,name,NULL,bot_id",
        ];
    }

    public function validatedData() {
        return $this->only([
            'name'
        ]);
    }
}
