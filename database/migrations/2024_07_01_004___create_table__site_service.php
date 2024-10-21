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
            $table->string('siteName')->nullable();
            $table->string('url')->nullable();
            $table->enum('is_active',['active','inactive'])->default('active');
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
