<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserRegistrationRequest extends FormRequest
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
            'name' => 'required|alpha|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8'
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => 'Email is required!',
            'name.required' => 'Name is required!',
            'password.required' => 'Password is required!'
        ];
    }


    public function getRegistrationData()
    {
        return [
            'name' => $this->input('name'),
            'email' => $this->input('email'),
            'password' => bcrypt($this->input('password'))
        ];
    }

}
