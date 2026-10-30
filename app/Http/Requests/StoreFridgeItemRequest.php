<?php

namespace App\Http\Requests;

use App\Enums\UnitLabel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFridgeItemRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:255'],
            'expires_at' => ['required', 'date'],
            'unit_count' => ['required', 'integer', 'min:1'],
            'unit_label' => ['nullable', Rule::enum(UnitLabel::class)],
        ];
    }
}
