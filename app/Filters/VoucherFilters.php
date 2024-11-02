<?php

namespace App\Filters;

use App\Models\FinanceTransaction;
use Carbon\Carbon;
use \App\Models\Transmission;

abstract class VoucherFilters
{
    protected $totalPerDay = 0;
    protected $totalPerMonth = 0;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function filters()
    {
        $today = Carbon::now()->toDateString();
        $todayVoucher = FinanceTransaction::PurchaseLimit($today)->get();
        $inMount = Carbon::now()->format('m');
        $toMountVoucher = FinanceTransaction::PurchaseLimit(mount: $inMount)->get();
        $transmissionInMount=Transmission::TransmissionLimit(true)->sum('payment_amount');
        $transmissionInDay=Transmission::TransmissionLimit()->sum('payment_amount');

        foreach ($todayVoucher as $voucher) {
            $this->totalPerDay += $voucher->voucher->voucherAmount();
        }
        foreach ($toMountVoucher as $voucher) {
            $this->totalPerMonth += $voucher->voucher->voucherAmount();
        }
        if ($this->totalPerDay >= env('Daily_Purchase_Limit')) {
            return $this->DailyPurchase();
        }
        if ($this->totalPerMonth >= env('Monthly_Purchase_Limit')) {
            return $this->MonthlyPurchase();
        }
        if ($transmissionInDay >= env('Daily_Purchase_Limit'))
        {
            return $this->DailyPurchase();
        }
        if ($transmissionInMount >= env('Monthly_Purchase_Limit'))
        {
            return $this->MonthlyPurchase();
        }
        return true;
    }

    abstract function DailyPurchase():string;
    abstract function MonthlyPurchase():string;
}

