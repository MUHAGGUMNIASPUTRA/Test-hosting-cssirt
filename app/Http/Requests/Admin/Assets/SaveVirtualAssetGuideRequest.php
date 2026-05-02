<?php

namespace App\Http\Requests\Admin\Assets;

use App\Enums\VirtualGuideType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveVirtualAssetGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::enum(VirtualGuideType::class)],
            'document_ids' => ['nullable', 'array'],
            'document_ids.*' => ['uuid', 'exists:documents,id'],
        ];
    }
}
