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
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
            'existing_attachment_ids' => ['nullable', 'array'],
            'existing_attachment_ids.*' => ['integer'],
        ];
    }
}
