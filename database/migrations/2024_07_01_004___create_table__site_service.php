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
        Schema::create('site_services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('domain')->nullable();
            $table->string('tron_wallet')->nullable();
            $table->string('products')->nullable();
            $table->string('person_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('license_image')->nullable();
            $table->string('is_active',['active','inactive'])->default('active');
            $table->string('token')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_services');
    }
};
