<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AskAiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'Pertanyaan tidak boleh kosong.',
            'question.max' => 'Pertanyaan maksimal 2000 karakter.',
        ];
    }
}
