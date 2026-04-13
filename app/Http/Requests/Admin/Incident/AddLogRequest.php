<?php

namespace App\Http\Requests\Admin\Incident;

use Illuminate\Foundation\Http\FormRequest;

class AddLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'log_message' => ['required', 'string'],
        ];
    }
}
