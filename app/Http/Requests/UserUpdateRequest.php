<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'registration_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => 'sometimes|string|min:8',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.max' => 'O nome não pode ter mais de 255 caracteres',
            'email.email' => 'O email deve ser um endereço válido',
            'email.unique' => 'Este email já está em uso',
            'registration_number.unique' => 'Este número de cadastro já está em uso',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres',
        ];
    }
}
