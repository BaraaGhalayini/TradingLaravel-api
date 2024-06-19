<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencieRequest extends FormRequest
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

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            // قواعد التحقق عند التحديث
            return $this->updateRules();
        }

        return [
            'name' => 'required|string|max:255',
            'entry_price' => 'required|numeric',
            'tp1' => 'required|numeric',
            'tp2' => 'required|numeric',
            'tp3' => 'required|numeric',
            'tp4' => 'nullable|numeric',
            'tp5' => 'nullable|numeric',
            'status' => 'required|string|in:buy,sell',
            'sgy_type' => 'required|string|max:50',
        ];
    }

    private function updateRules()
    {
        return [
            'status' => 'required|string|in:buy,sell',
        ];
    }

}
