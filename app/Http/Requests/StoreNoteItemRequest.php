<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteItemRequest extends FormRequest
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
        return [
            "customer"  => "required|uuid",
            "item"      => "required|uuid",
            "quantity"  => "required|integer|min:1",
            "file"      => "nullable|file|mimes:png,jpg,pdf,docx",
        ];
    }

    public function attributes(): array
    {
        return [
            "customer"  => "cliente",
            "item"      => "item",
            "quantity"  => "cantidad",
            "file"      => "archivo",
        ];
    }
}
