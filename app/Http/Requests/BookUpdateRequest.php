<?php

namespace App\Http\Requests;

use App\Enums\BookGenreEnum;
use App\Enums\BookStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class BookUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autorização será tratada via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book');

        return [
            'title' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'registration_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('books')->ignore($bookId),
            ],
            'genre' => ['sometimes', new Enum(BookGenreEnum::class)],
            'status' => ['sometimes', new Enum(BookStatusEnum::class)],
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
            'title.max' => 'O título não pode ter mais de 255 caracteres',
            'author.max' => 'O autor não pode ter mais de 255 caracteres',
            'registration_number.unique' => 'Este número de registro já está em uso',
            'genre.Illuminate\Validation\Rules\Enum' => 'O gênero selecionado não é válido',
            'status.Illuminate\Validation\Rules\Enum' => 'O status selecionado não é válido',
        ];
    }
}
