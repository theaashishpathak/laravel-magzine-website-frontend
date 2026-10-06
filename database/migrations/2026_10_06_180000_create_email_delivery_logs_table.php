<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_delivery_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('recipient_email')->index();
            $table->string('recipient_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('subject');
            $table->string('mailer')->default('smtp');
            $table->string('status', 30)->default('pending')->index(); // pending, delivered, failed
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();

            $table->index(['created_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_delivery_logs');
    }
};
