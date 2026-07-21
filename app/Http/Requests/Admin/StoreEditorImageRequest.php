<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEditorImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'File gambar wajib dipilih.',
            'image.image' => 'File harus berupa gambar yang valid.',
            'image.mimes' => 'Format gambar hanya JPG, PNG, WebP, atau GIF.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}
