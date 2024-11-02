<?php

namespace App\Jobs;

use App\Http\Traits\HasConfig;
use App\Models\Ticket;
use App\Models\TransmissionsBank;
use AyubIRZ\PerfectMoneyAPI\PerfectMoneyAPI;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class transmissionsBankArrangementJob implements ShouldQueue
{
    use Queueable,HasConfig;
    protected $inputsConfig = null;


    public $timeout = 0;
    protected $numberOFVouchers =
        [
            1 => 10,
            2 => 10,
            3 => 5,
            4 => 5,
            5 => 7,
            6 => 5,
            7 => 2,
            8 => 2,
            9 => 2,
            10 => 4,
            20 => 4,
            16 => 1,
            17 => 1,
            18 => 1,
            25 => 1

        ];



    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->inputsConfig=$this;
    }
    public function middleware(): array
    {
        return [new WithoutOverlapping('transmissionsBankArrangementJob')];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        try {
            foreach ($this->numberOFVouchers as $amount => $numberOFVoucher) {
                $getNewVoucherInDatabaseTable = TransmissionsBank::where('status', 'new')->where("payment_amount", $amount)->where('type','sainaex')->count();

                $numberOfGenerate = $numberOFVoucher - $getNewVoucherInDatabaseTable;

                if ($numberOfGenerate > 0) {

                    for ($i = 0; $i < $numberOfGenerate; $i++) {
                        $PMeVoucher = $this->customVoucherTransfer($amount);
                        if ($PMeVoucher)
                            sleep(2);
                        else
                            Log::emergency(json_encode($PMeVoucher));
                    }
                }

            }
        } catch (\Exception $e) {
            Log::emergency(PHP_EOL . $e->getMessage() . PHP_EOL);
            Ticket::create([
                'subject' => 'خرابی در ساخت اتوماتیک شماره پیگیری حواله',
                'user_id' => 1,
                'status' => 'closed'
            ]);
        }

    }
}
