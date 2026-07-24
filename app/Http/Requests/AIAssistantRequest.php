<?php

// File: app/Http/Requests/AIAssistantRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AIAssistantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Public endpoint — semua user boleh mengakses.
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
            'question' => [
                'required',
                'string',
                'min:3',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question.required' => 'Pertanyaan tidak boleh kosong.',
            'question.min'      => 'Pertanyaan terlalu pendek (minimal 3 karakter).',
            'question.max'      => 'Pertanyaan terlalu panjang (maksimal 500 karakter).',
        ];
    }
}
