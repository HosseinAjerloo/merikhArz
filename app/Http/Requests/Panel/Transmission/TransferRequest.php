<?php

namespace App\Http\Requests\Panel\Transmission;

use App\Rules\Panel\Transmission\DecimalRule;
use App\Rules\PAYERACCOUNTRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            "custom_payment"=>['required','numeric',"max:".env('Daily_Purchase_Limit'),'min:0.1',new DecimalRule()],

            "transmission"=>["required","max:9","min:9",new PAYERACCOUNTRule()],
        ];
    }
    public function messages(): array
    {
        return [
            'custom_payment.required'=>'وارد کردن مبلغ کارت هدیه پرفکت مانی الزامی است',
            'siteService.required'=>'پذیرنده نامعتبر است',
            'siteService.exists'=>'پذیرنده نامعتبر است',
        ];
    }
}
