<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\FullName;
use Illuminate\Foundation\Http\FormRequest;

class AuthRegisterRequest extends FormRequest
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
            'name' => ['required', new FullName(2)],
            'email' => ['required','email:rfc,dns','unique:users'],
            'password' => ['required','confirmed','min:8'],
        ];
    }
}
