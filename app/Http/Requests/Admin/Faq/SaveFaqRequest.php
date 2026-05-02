<?php

namespace App\Http\Requests\Admin\Faq;

use Illuminate\Foundation\Http\FormRequest;

class SaveFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'min:5'],
            'answer' => ['required', 'string', 'min:10'],
            'category' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ];
    }
}
