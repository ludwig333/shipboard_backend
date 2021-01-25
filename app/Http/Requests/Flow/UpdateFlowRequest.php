<?php

namespace App\Http\Requests\Flow;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Flow;
use App\Rules\ValidateUniqueFlowName;

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
        $botId = $this->route('flow')->bot_id;
        return [
            'name' => ['required','string','min:3', new ValidateUniqueFlowName($botId)],
        ];
    }

    public function validatedData() {
        return $this->only([
            'name'
        ]);
    }
}
