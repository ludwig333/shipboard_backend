<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Flow;

class CreateMessageRequest extends FormRequest
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
            'name' => 'required',
            'type' => 'sometimes',
            'flow' =>  'required|exists:flows,uuid',
            'position_x' => 'required',
            'position_y' => 'required',
        ];
    }

    public function validatedData() {
        $flow = Flow::where('uuid', $this->input('flow'))->first();
        return [
            'name' => $this->input('name'),
            'type' => $this->input('type'),
            'flow_id' => $flow->id,
            'position_x' => $this->input('position_x'),
            'position_y' => $this->input('position_y')
        ];
    }
}
