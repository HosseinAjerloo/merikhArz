<?php

namespace App\Http\Requests\Panel\Utopia;

use Illuminate\Foundation\Http\FormRequest;

class UtopiaRequest extends FormRequest
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
            'bank'=>'sometimes|exists:banks,id',
            'custom_payment'=>'required|decimal:0,1|max:'.env('Daily_Purchase_Limit')
        ];
    }
}
