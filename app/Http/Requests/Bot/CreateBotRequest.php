<?php

namespace App\Http\Requests\Bot;

use Illuminate\Foundation\Http\FormRequest;

class CreateBotRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255|unique:bots,name,user_id',
        ];
    }

    /**
     * Get the input values from create bot form
     *
     * @return array
     */
    public function validatedData()
    {
        $data = $this->only([
            'name',
        ]);

        $data['user_id'] = auth()->user()->id;

        return $data;
    }
}
