<?php

namespace App\Http\Requests\Flow;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Bot;
use App\Models\Flow;
use Illuminate\Validation\Rule;
use App\Rules\ValidateUniqueFlowName;

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
        $botId = Bot::where('uuid', $this->input('bot'))->first()->id;

        return [
            'name' => ['required','string','min:3', new ValidateUniqueFlowName($botId)],
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
