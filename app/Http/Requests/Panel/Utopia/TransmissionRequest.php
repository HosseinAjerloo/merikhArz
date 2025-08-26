<?php

namespace App\Http\Requests\Panel\Utopia;

use App\Rules\Panel\Transmission\DecimalRule;
use Illuminate\Foundation\Http\FormRequest;

class TransmissionRequest extends FormRequest
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
//            'Accepting_the_rules'=>"nullable|in:on",
            "custom_payment"=>[(request()->has('service_id')==false?'required':'nullable'),'sometimes','numeric',"max:".env('Daily_Purchase_Limit'),'min:0.1',new DecimalRule()],
            "transmission"=>["required","max:64","min:64"]
        ];
    }
    public function messages(): array
    {
        return [
            'custom_payment.required'=>'انتخاب مبلغ حواله الزامی است'
        ];
    }
}
