<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FastPayment extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable=['invoice_id','finance_id','amount','account','pay_id','url_back','api_success'];
    public function financeTransaction()
    {
        return $this->belongsTo(FinanceTransaction::class,'finance_id');
    }
}
