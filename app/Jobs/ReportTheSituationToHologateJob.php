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

class ReportTheSituationToHologateJob implements ShouldQueue, ShouldBeUnique
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

    public function uniqueId(): string
    {
        return $this->fastPayment->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::emergency('fastPayment :' . $this->fastPayment->id);

        try {
            $response = Http::timeout(50)->withHeaders([
                'token' => env('SAINAEX_TOKEN')
            ])->withoutVerifying()->post(env('SAINAEX_REQUEST_REPORT_PAYMENT'),
                [
                    'success' => ($this->fastPayment->financeTransaction->payment->state)!='finished'?false:true,
                    'invoice_id' => $this->fastPayment->pay_id,
                    'amount' => $this->fastPayment->amount,
                    'phone_number' => $this->fastPayment->financeTransaction->user->mobile
                ]);

        } catch (\Exception $exception) {
            Log::emergency('con not connection to request host ' . env('SAINAEX_REQUEST_REPORT_PAYMENT') . ' ' . $exception->getMessage());
            SendAppAlertsJob::dispatch('خطا در برقراری ارتباط داخلی سرور')->onQueue('perfectmoney');
        }
    }
}
