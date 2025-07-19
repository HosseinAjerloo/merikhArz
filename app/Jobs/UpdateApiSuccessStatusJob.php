<?php

namespace App\Jobs;

use App\Models\FastPayment;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateApiSuccessStatusJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('UpdateApiSuccess');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $fastPayments = FastPayment::where('api_success', 'false')->get();
            foreach ($fastPayments as $fastPayment) {
                $response = Http::timeout(70)->withHeaders([
                    'token' => env('SAINAEX_TOKEN')
                ])->withoutVerifying()->post(env('SAINAEX_REQUEST_REPORT_PAYMENT'),
                    [
                        'success' => ($fastPayment->financeTransaction->payment->state) != 'finished' ? false : true,
                        'invoice_id' => $fastPayment->pay_id,
                        'amount' => $fastPayment->amount,
                        'phone_number' => $fastPayment->financeTransaction->user->mobile
                    ]);
                if ($response->status() == 200 and $response->successful()) {
                    $fastPayment->api_success = 'true';
                    $fastPayment->save();
                }

            }
        } catch (\Exception $exception) {
            Log::emergency('con not connection to request host ' . env('SAINAEX_REQUEST_REPORT_PAYMENT') . ' ' . $exception->getMessage());
            SendAppAlertsJob::dispatch('خطا در ارتباط با مقصد وجود آمد لطفا شبکه راچک کنید')->onQueue('perfectmoney');
        }
    }
}
