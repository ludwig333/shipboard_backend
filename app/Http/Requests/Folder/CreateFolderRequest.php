<?php

namespace App\Http\Requests\Folder;

use Illuminate\Foundation\Http\FormRequest;

class CreateFolderRequest extends FormRequest
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
            'flow_id' => 'required',
            'parent_id' => 'required|sometimes'
        ];
    }

    public function validatedData()
    {
        return [
            'name' => $this->input('name'),
            'flow_id' => $this->input('flow_id'),
            'parent_folder_id' => $this->has('parent_id') ? $this->input('parent_id') : 0
        ];
    }
}
