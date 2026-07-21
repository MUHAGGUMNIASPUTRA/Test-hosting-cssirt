<?php

namespace App\Http\Requests\Admin\Post;

use App\Enums\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'excerpt' => ['required', 'string', 'max:500'],
            'image_type' => ['nullable', 'in:file,link'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', 'required_if:image_type,file'],
            'image_url' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::enum(PostStatus::class)],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul artikel wajib diisi.',
            'title.max' => 'Judul artikel maksimal 255 karakter.',
            'body.required' => 'Isi artikel wajib diisi.',
            'excerpt.required' => 'Ringkasan artikel wajib diisi.',
            'excerpt.max' => 'Ringkasan artikel maksimal 500 karakter.',
            'image_type.in' => 'Tipe gambar hanya File atau Link.',
            'image.required_if' => 'Gambar wajib dipilih jika tipe gambar adalah File.',
            'image.image' => 'File harus berupa gambar yang valid.',
            'image.mimes' => 'Format gambar hanya JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
            'image_url.max' => 'URL gambar terlalu panjang (maks. 1000 karakter).',
            'status.required' => 'Status publikasi wajib dipilih.',
            'categories.required' => 'Minimal satu kategori wajib dipilih.',
            'categories.min' => 'Minimal satu kategori wajib dipilih.',
        ];
    }
}
