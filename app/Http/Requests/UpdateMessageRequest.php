<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Message;

class UpdateMessageRequest extends FormRequest
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
            'name' => 'sometimes|min:3',
            'position_x' => 'sometimes',
            'position_y' => 'sometimes',
            'next' => 'sometimes|exists:messages,uuid'
        ];
    }

    public function validatedData() {
        $data = [
            'name' => $this->input('name'),
            'position_x' => $this->input('position_x'),
            'position_y' => $this->input('position_y'),
        ];

        if(!$this->has('name')) {
            unset($data['name']);
        }
        if(!$this->has('position_x')) {
            unset($data['position_x']);
        }
        if(!$this->has('position_y')) {
            unset($data['position_y']);
        }
        if($this->has('next')) {
            $data['next_message_id'] = Message::where('uuid', $this->input('next'))->first()->id;
        }

        return $data;
    }
}
