<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fast_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('finance_id')->nullable()->constrained('finance_transactions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('amount')->nullable();
            $table->string('account')->nullable();
            $table->string('pay_id')->nullable();
            $table->string('url_back')->nullable();
            $table->enum('api_success',['true','false'])->default('false');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fast_payments');
    }
};
