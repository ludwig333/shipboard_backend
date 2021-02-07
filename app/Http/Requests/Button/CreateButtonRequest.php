<?php

namespace App\Http\Requests\Button;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Text;
use App\Models\Card;

class CreateButtonRequest extends FormRequest
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
            'parent_type' => 'required',
            'parent' => 'required'
        ];
    }

    public function validatedData() {
        $parent = null;
        $parentType = null;

        if($this->input('parent_type') == "text") {
            $parent = Text::where('uuid', $this->input('parent'))->first();
            $parentType = Text::class;
        } else if ($this->input('parent_type') == "card") {
            $parent = Card::where('uuid', $this->input('parent'))->first();
            $parentType = Card::class;
        }
        $data = [
            'name' => $this->input('name'),
            'parent' => $parentType,
            'parent_id' => $parent->id
        ];
        return $data;
    }
}
