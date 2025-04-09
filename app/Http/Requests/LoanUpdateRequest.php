<?php

namespace App\Http\Requests;

use App\Enums\LoanStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class LoanUpdateRequest extends FormRequest
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
        return [
            'status' => ['sometimes', new Enum(LoanStatusEnum::class)],
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
            'status.Illuminate\Validation\Rules\Enum' => 'O status selecionado não é válido',
            'due_date.date' => 'A data de devolução deve ser uma data válida',
            'due_date.after' => 'A data de devolução deve ser após hoje',
        ];
    }
}
