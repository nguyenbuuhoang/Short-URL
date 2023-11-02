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
        Schema::create('short_urls', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('short_url_link')->nullable();
            $table->string('short_code')->unique();
            $table->integer('clicks')->default(0);
            $table->string('status')->default('active');
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->binary('qrcode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_urls');
    }
};
