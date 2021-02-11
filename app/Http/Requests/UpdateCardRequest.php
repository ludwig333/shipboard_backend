<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
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
            'heading' => 'sometimes',
            'body' => 'sometimes',
            'height' => 'sometimes'
        ];
    }

    public function validatedData() {
        $data = [
            'title' => $this->input('heading'),
            'body' => $this->input('body'),
            'height' => $this->input('height')
        ];
        if(!$this->has('heading')) {
            unset($data['title']);
        } else if(!$this->has('body')) {
            unset($data['body']);
        } else if(!$this->has('height')) {
            unset($data['height']);
        }
        return $data;
    }
}
