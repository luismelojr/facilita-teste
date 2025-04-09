<?php

namespace App\Http\Requests;

use App\Enums\BookGenreEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class BookStoreRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'registration_number' => 'required|string|max:50|unique:books',
            'genre' => ['required', new Enum(BookGenreEnum::class)],
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
            'title.required' => 'O título é obrigatório',
            'title.max' => 'O título não pode ter mais de 255 caracteres',
            'author.required' => 'O autor é obrigatório',
            'author.max' => 'O autor não pode ter mais de 255 caracteres',
            'registration_number.required' => 'O número de registro é obrigatório',
            'registration_number.unique' => 'Este número de registro já está em uso',
            'genre.required' => 'O gênero é obrigatório',
            'genre.Illuminate\Validation\Rules\Enum' => 'O gênero selecionado não é válido',
        ];
    }
}
