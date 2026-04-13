<?php

namespace App\Http\Requests\Admin\Incident;

use App\Enums\IncidentPriority;
use App\Enums\IncidentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reporter_name' => ['required', 'string', 'max:255'],
            'reporter_email' => ['required', 'email', 'max:255'],
            'reporter_phone' => ['nullable', 'string', 'max:20'],
            'incident_type_id' => ['required', 'exists:incident_types,id'],
            'incident_at' => ['required', 'date'],
            'description' => ['required', 'string'],
            'status' => ['required', Rule::enum(IncidentStatus::class)],
            'priority' => ['required', Rule::enum(IncidentPriority::class)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'attachment_type' => ['nullable', 'in:file,link'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,zip,doc,docx', 'max:5120'],
            'attachment_links' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
