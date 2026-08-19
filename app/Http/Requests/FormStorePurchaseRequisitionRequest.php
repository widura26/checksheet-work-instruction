<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FormStorePurchaseRequisitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin') ? true : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'no_pr' => [
                'required',
                'string',
                'max:50',
                'unique:purchase_requisitions,no_pr',
            ],

            'tanggal_pr' => [
                'required',
                'date',
            ],

            'detail_kerusakan_tools_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'detail_kerusakan_tools_ids.*' => [
                'required',
                'integer',
                'exists:detail_kerusakan_tools,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'detail_kerusakan_tools_ids.required' => 'Minimal pilih satu tool rusak.',
            'detail_kerusakan_tools_ids.array' => 'Format tool rusak tidak valid.',
            'detail_kerusakan_tools_ids.min' => 'Minimal pilih satu tool rusak.',
        ];
    }
}
