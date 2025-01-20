<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Doller;
use App\Models\FinanceTransaction;
use App\Models\Invoice;
use App\Models\SiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentServiceController extends Controller
{

    public function payment_service_register()
    {

        return view('Panel.FastPayment.payment-service-register');
    }
    public function payment_service_register_submit(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name'=>['required'],
                'domain'=>['required'],
                'tron_wallet'=>['required'],
                'products'=>['required'],
                'person_name'=>['required'],
                'email'=>['required'],
                'phone'=>['required'],
                'license_image'=>['required'],
            ]
        );
        if ($validator->fails())
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();

        $file =$request->file('license_image');
        $filename = time().'_'.$file->getClientOriginalName();
        $saved_file = $request->file('license_image')->move(public_path('images/paymentService'), $filename);

        SiteService::create([
            'name'=> $request->name,
            'domain'=>$request->domain,
            'tron_wallet'=>$request->tron_wallet,
            'products'=>$request->products,
            'person_name'=>$request->person_name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'license_image' => '/images/paymentService/'.$filename
        ]);

        return redirect()->back()->with(['success'=>"درخواست شما با موفقیت ثبت شد"]);
    }
}
