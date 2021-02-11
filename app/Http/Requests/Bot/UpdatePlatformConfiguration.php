<?php

namespace App\Http\Requests\Bot;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Constants\PlatformConstant;
use App\Constants\PlatformType;

class UpdatePlatformConfiguration extends FormRequest
{/**
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
            'username' => 'sometimes|required',
            'verification_code' => 'sometimes|required',
            'client_id' => 'sometimes|required',
            'client_secret' => 'sometimes|required',
            'access_token' => 'sometimes|required',
            'connect_status' => [
                'required',
                Rule::in([0, 1])
            ]
        ];
    }

    public function validatedData()
    {
        $platform = $this->get('platform');
        if ( $platform == PlatformType::TELEGRAM ) {
            return $this->only( [
                'username',
                'access_token',
                'connect_status'
            ]);

        } else if ( $platform == PlatformType::MESSENGER ) {
            return $this->only( [
                'verification_code',
                'access_token',
                'connect_status',
                'app_secret'
            ]);
        } else if ( $platform == PlatformType::SLACK) {
            return $this->only( [
                'access_token',
                'connect_status'
            ]);
        }
    }
}
