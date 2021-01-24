<?php

namespace App\Http\Requests\Flow;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Bot;
use App\Models\Flow;

class CreateFlowRequest extends FormRequest
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
     * Get the validation rules that apply to the.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => "required|string|min:3|unique:flows,name,NULL,bot_id",
            'bot' => 'required|exists:bots,uuid'
        ];
    }

    public function validatedData()
    {
        $bot = Bot::where('uuid', $this->input('bot'))->first();
        return  [
            'name' => $this->input('name'),
            'bot_id' => $bot->id
        ];
    }
}
