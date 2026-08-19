<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DaftarKaryawanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'nip' => [
                'required',
                'string',
                'max:50',
            ],
            'nip' => $this->isMethod('post') ? ['required', 'string', 'max:50', 'unique:daftar_karyawans,nip'] : ['required', 'string', 'max:50', Rule::unique('daftar_karyawans', 'nip')->ignore($this->karyawan)],
            'nama' => ['required', 'string', 'max:255'],
            'departemen_id' => ['nullable', 'exists:departemens,id'],
            'bagian_id' => ['nullable', 'exists:bagians,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'jabatan' => ['required', 'string', 'max:255'],
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nip.string' => 'NIP harus berupa teks.',
            'nip.max' => 'NIP tidak boleh lebih dari 50 karakter.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'departemen_id.exists' => 'Departemen tidak valid.',
            'bagian_id.exists' => 'Bagian tidak valid.',
            'unit_id.exists' => 'Unit tidak valid.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'jabatan.string' => 'Jabatan harus berupa teks.',
            'jabatan.max' => 'Jabatan tidak boleh lebih dari 255 karakter.',
        ];
    }
}
