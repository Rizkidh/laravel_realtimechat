<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_id.required' => 'Penerima pesan harus dipilih.',
            'receiver_id.exists' => 'Pengguna penerima tidak ditemukan.',
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.max' => 'Pesan maksimal 5000 karakter.',
        ];
    }
}
