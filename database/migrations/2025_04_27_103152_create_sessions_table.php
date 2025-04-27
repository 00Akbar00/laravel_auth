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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->constrained('users');
            $table->char('sessionId', 64);
            $table->string('ipAddress', 45);
            $table->text('userAgent');
            $table->json('metadata')->nullable();
            $table->timestamp('expiresAt');
            $table->timestamp('createdAt')->useCurrent();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
