<?php

namespace App\Http\Requests\Button;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Message;

class UpdateButtonRequest extends FormRequest
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
            'next' => 'sometimes|exists:messages,uuid'
        ];
    }

    public function validatedData() {
        $data = [];
        if($this->has('next')) {
            $message = Message::where('uuid', $this->input('next'))->first();
            $data['leads_to_message'] = $message->id;
        }

        return $data;
    }
}
