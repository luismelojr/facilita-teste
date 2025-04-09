<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanStoreRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id',
            'book_id' => 'required|integer|exists:books,id',
            'due_date' => 'sometimes|date|after:today',
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
            'user_id.required' => 'O ID do usuário é obrigatório',
            'user_id.exists' => 'O usuário selecionado não existe',
            'book_id.required' => 'O ID do livro é obrigatório',
            'book_id.exists' => 'O livro selecionado não existe',
            'due_date.date' => 'A data de devolução deve ser uma data válida',
            'due_date.after' => 'A data de devolução deve ser após hoje',
        ];
    }
}
