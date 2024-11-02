<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Transmission extends Model
{
    const StartWith = 100000;
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'finance_id', 'payee_account_name', 'payee_account', 'payer_account', 'payment_amount', 'payment_batch_num', 'invoice_id', 'type'];

    public function financeTransaction()
    {
        $this->belongsTo(FinanceTransaction::class, 'finance_id');
    }

    public function ScopeTransmissionLimit(Builder $builder, $mount = false)
    {
        $user = Auth::user();
        $carbon=Carbon::now();
        if (!$mount)
        {
            $builder->where('user_id', $user->id)->whereDate('created_at', $carbon->toDateString());
        }
        else{
            $builder->where('user_id', $user->id)->whereMonth('created_at', $carbon->format('m'));
        }
    }
}
