<?php

namespace App\Http\Traits;


use App\Models\FastPayment;
use App\Models\Transmission;
use Illuminate\Http\Request;

trait HasFindUser
{
    protected function finderUser(Request $request)
    {
        if ($request->input('batch_key')) {
            $transmission = Transmission::where('payment_batch_num', $request->input('batch_key'))->first();
            if (!$transmission)
                return $this->failMessage();

            return response()->json(['success'=>true,'data'=>$transmission->user,'message'=>'پردازش باموفقیت رکورد شما در فیلد دیتا موجود میباشد']);
        } else {
            $fastPayment = FastPayment::where('pay_id', $request->input('order_key'))->first();
            if (!$fastPayment)
                return $this->failMessage();

            return response()->json(['success'=>true,'data'=>$fastPayment->financeTransaction->user,'message'=>'پردازش باموفقیت رکورد شما در فیلد دیتا موجود میباشد']);


        }
    }

    protected function failMessage()
    {
        return response()->json(['success' => false, 'message' => 'رکورد یافت نشد','data'=>[]]);
    }
}
