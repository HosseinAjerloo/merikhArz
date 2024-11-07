<?php

namespace App\Http\Traits;


use App\Jobs\SendAppAlertsJob;
use App\Models\Invoice;
use App\Models\Transmission;
use App\Models\TransmissionsBank;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VouchersBank;
use App\Rules\Panel\Transmission\DecimalRule;
use App\Rules\PAYERACCOUNTRule;
use AyubIRZ\PerfectMoneyAPI\PerfectMoneyAPI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;


trait HasConfig
{
    protected $PMeVoucher = null;
    protected $redirectTo = 'panel.purchase.view';
    protected $message;

    protected $purchasePermitStatus = false;


    public function validationFiledUser()
    {
        $classOpp = get_class();
        if (isset($classOpp) and $classOpp == 'App\Models\User') {
            $user = $this;
        } else {
            $user = Auth::user();
        }
        if ($user) {
            if (empty($user->name) || empty($user->family) || empty($user->national_code) || empty($user->mobile) || empty($user->tel) || empty($user->address) || empty($user->email)) {
                return true;
            } else return false;
        }

    }

    protected function generateVoucher($amount)
    {
        $voucher = VouchersBank::where('status', 'new')->where("amount", $amount)->first();
        if ($voucher) {
            $this->PMeVoucher['VOUCHER_NUM'] = $voucher->serial;
            $this->PMeVoucher['VOUCHER_CODE'] = $voucher->code;
            $voucher->update(['status' => 'used']);
        } else {
            $PM = new PerfectMoneyAPI(env('PM_ACCOUNT_ID'), env('PM_PASS'));
            $PMeVoucher = $PM->createEV(env('PAYER_ACCOUNT'), $amount);
            if (is_array($PMeVoucher) and isset($PMeVoucher['VOUCHER_NUM']) and isset($PMeVoucher['VOUCHER_CODE'])) {
                $this->PMeVoucher = $PMeVoucher;
            }
        }

    }

    protected function transmission($transmission, $amount)
    {

        if ($transmission != env('DESTINATION_REMITTANCE'))
            return $this->transmissionVoucher($transmission, $amount);

        $transmissionBank = TransmissionsBank::where('status', 'new')->where('payment_amount', $amount)->where('type','sainaex')->first();
        if ($transmissionBank) {
            $this->inputsConfig->type = 'sainaex';
            return $this->sendReference($transmissionBank);
        } else if ($customVoucherTransfer = $this->customVoucherTransfer($amount)) {
            return $this->sendReference($customVoucherTransfer);
        } else {
            return false;
        }
    }

    protected function sendReference(TransmissionsBank $transmissionBank): array
    {
        $transmissionBank->update(['status'=> 'used']);
        $PMeVoucher = [];
        $PMeVoucher['PAYMENT_AMOUNT'] = $transmissionBank->payment_amount;
        $PMeVoucher['PAYMENT_BATCH_NUM'] = $transmissionBank->payment_batch_num;
        $PMeVoucher['Payer_Account'] = env('PAYER_ACCOUNT');
        $PMeVoucher['Payee_Account'] = env('DESTINATION_REMITTANCE');
        $PMeVoucher['Payee_Account_Name'] = 'hologate4';
        return $PMeVoucher;
    }


    protected function transmissionVoucher($transmission, $amount)
    {
        $PM = new PerfectMoneyAPI(env('PM_ACCOUNT_ID'), env('PM_PASS'));
        $PMeVoucher = $PM->transferFund(env('PAYER_ACCOUNT'), $transmission, $amount);
        if (is_array($PMeVoucher) and isset($PMeVoucher['PAYMENT_BATCH_NUM']) and isset($PMeVoucher['Payee_Account'])) {
            return $PMeVoucher;
        } else {
            Log::emergency('The transfer did not take place, the reason for its failure: ' . json_encode($PMeVoucher));
            return false;
        }
    }

    protected function purchasePermit($invoice, $payment)
    {
        $user = Auth::user();
        $balance = Auth::user()->getCreaditBalance();
        $invoice = Invoice::where('id', $invoice->id)->where('user_id', $user->id)->where("status", 'finished')->first();
        $voucher = $invoice->voucher;

        if ($voucher) {
            $this->message = ['error' => "این سفارش قبلا توسط شما خریداری شده است لطفا جهت مشاهده سفارش از منوی داشبورد به قسمت سفارشات مراجعه فرمایید. "];
            $this->purchasePermitStatus = true;
            return $this;
        }

        if ($balance < $payment->amount) {
            $this->message = ['error' => "شارژ کیف پول  شما جهت خریداری کارت هدیه کافی نمیباشد لطفا کیف پول خود را شارژ فرمایید و مجددا خرید فرمایید.  "];
            $this->purchasePermitStatus = true;
            return $this;
        }
        return $this;


    }

    protected function redirectFunction()
    {
        return redirect()->route($this->redirectTo)->withErrors($this->message);
    }


    protected function transferValidation()
    {
        return Validator::make(request()->all(),
            [
                'amount' => ['required', 'numeric', "max:" . env('Daily_Purchase_Limit'), 'min:0.1', new DecimalRule()],
                'account' => ["required", "min:9", "max:9", new PAYERACCOUNTRule()]
            ],
            [
                'amount.required' => 'وارد کردن مبلغ حواله الزامی است',
                'amount.numeric' => 'مبلغ حواله باید به صورت عددی باشد',
                'amount.max' => 'مبلغ حواله نباید بزرگ تر از 20 باشد',
                'amount.min' => 'مبلغ حواله نباید کوچک  تر از 1 باشد',
                'account.required' => 'وارد کردن شماره حساب حواله الزامی است',
                'account.max' => 'حداکثر طول شماره حساب حواله باید 9 کاراکتر باشد',
                'account.min' => 'حداقل طول شماره حساب حواله باید 9 کاراکتر باشد',
            ]
        );
    }

    protected function customVoucherTransfer($amount)
    {

        $lastRecord = TransmissionsBank::where('type', 'sainaex')->latest()->first();
        $this->inputsConfig->payment_amount = $amount;
        $this->inputsConfig->type = 'sainaex';
        $randBatch = rand(111, 999);
        if (!$lastRecord) {
            $this->inputsConfig->payment_batch_num = TransmissionsBank::StartWith . $randBatch;
        } else {
            $payment_batch_num = (TransmissionsBank::StartWith + $lastRecord->id) + 1;
            $this->inputsConfig->payment_batch_num = $payment_batch_num . $randBatch;
        }

        if ($this->requestToHost()) {
            return TransmissionsBank::create(
                [
                    'payment_amount' => $this->inputsConfig->payment_amount,
                    'payment_batch_num' => $this->inputsConfig->payment_batch_num,
                    'status' => 'new',
                    'description' => 'انتقال ووچر به صورت اتوماتیک',
                    'type' => $this->inputsConfig->type,
                ]
            );
        }
        return false;
    }

    protected function requestToHost(): bool
    {
        try {
            $response = Http::timeout(50)->withHeaders([
                'token' => env('SAINAEX_TOKEN')
            ])->withoutVerifying()->post(env('SAINAEX_REQUEST'),
                [
                    'batch' => $this->inputsConfig->payment_batch_num,
                    'currency' => 'USD',
                    'type' => 'sainaex',
                    'fee' => '0.00',
                    'payer_account' => env('PAYER_ACCOUNT'),
                    'payee_account' => env('DESTINATION_REMITTANCE'),
                    'memo' => "SAINAEX,Received Payment " . $this->inputsConfig->payment_amount . " USD from account  " . env('PAYER_ACCOUNT') . ". Memo: API Payment.",
                    'amount' => $this->inputsConfig->payment_amount
                ]);
            $body = json_decode($response->body());
            if (isset($body->success) and $body->success)
                return true;
            return false;
        } catch (\Exception $exception) {
            Log::emergency('con not connection to request host ' . env('SAINAEX_REQUEST') . ' ' . $exception->getMessage());
            SendAppAlertsJob::dispatch('خطا در برقراری ارتباط داخلی سرور')->onQueue('perfectmoney');
            return false;
        }
    }


}
