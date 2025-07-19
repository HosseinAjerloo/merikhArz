<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;

class ReportTheSituationToHologateJob implements ShouldQueue
{
    use Queueable;

    public $fastPayment;
    public $timeout = 0;

    /**
     * Create a new job instance.
     */
    public function __construct($fastPayment)
    {
        $this->fastPayment = $fastPayment;
    }



    /**
     * Execute the job.
     */
    public function handle(): void
    {

        try {
            $response = Http::timeout(70)->withHeaders([
                'token' => env('SAINAEX_TOKEN')
            ])->withoutVerifying()->post(env('SAINAEX_REQUEST_REPORT_PAYMENT'),
                [
                    'success' => ($this->fastPayment->financeTransaction->payment->state)!='finished'?false:true,
                    'invoice_id' => $this->fastPayment->pay_id,
                    'amount' => $this->fastPayment->amount,
                    'phone_number' => $this->fastPayment->financeTransaction->user->mobile
                ]);
            if ($response->status()==200 and $response->successful())
            {
                $this->fastPayment->api_success='true';
                $this->fastPayment->save();
            }

        } catch (\Exception $exception) {
            Log::emergency('con not connection to request host ' . env('SAINAEX_REQUEST_REPORT_PAYMENT') . ' ' . $exception->getMessage());
            SendAppAlertsJob::dispatch('خطا در ارتباط با مقصد وجود آمد لطفا شبکه راچک کنید')->onQueue('perfectmoney');
        }
    }
}
