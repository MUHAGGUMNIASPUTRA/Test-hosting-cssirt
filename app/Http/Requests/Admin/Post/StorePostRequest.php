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
            'title'        => ['required', 'string', 'max:255'],
            'body'         => ['required', 'string'],
            'excerpt'      => ['required', 'string', 'max:500'],
            'image_type'   => ['nullable', 'in:file,link'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'image_url'    => ['nullable', 'string', 'max:1000'],
            'status'       => ['required', Rule::enum(PostStatus::class)],
            'categories'   => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'tags'         => ['nullable', 'array'],
            'tags.*'       => ['exists:tags,id'],
        ];
    }
}
