<?php

namespace App\Http\Requests\Admin\Incident;

use App\Enums\IncidentPriority;
use App\Enums\IncidentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'      => ['required', Rule::enum(IncidentStatus::class)],
            'priority'    => ['required', Rule::enum(IncidentPriority::class)],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }
}
