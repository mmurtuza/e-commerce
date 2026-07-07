<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\BdPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'email', Rule::unique('users', 'email')->ignore(auth()->id())],
            'phone' => ['nullable', 'string', new BdPhone, Rule::unique('users', 'phone')->ignore(auth()->id())],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'locale' => ['nullable', 'string', Rule::in(['bn', 'en'])],
        ];
    }
}
