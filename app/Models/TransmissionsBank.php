<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransmissionsBank extends Model
{
    use HasFactory;
    const StartWith=200000;
    public $table='transmissions_bank';
    protected $fillable=['payment_amount','payment_batch_num', 'status','description','type'];

}
