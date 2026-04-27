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
        Schema::create('admin_chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 120)->nullable()->index();
            $table->string('question', 500);
            $table->string('normalized_question', 500)->nullable();
            $table->string('intent', 80)->index();
            $table->json('parameters')->nullable();
            $table->boolean('success')->default(false)->index();
            $table->text('response_summary')->nullable();
            $table->json('response_meta')->nullable();
            $table->json('context_snapshot')->nullable();
            $table->unsignedInteger('latency_ms')->default(0);
            $table->string('feedback', 20)->nullable()->index();
            $table->timestamp('feedback_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_chatbot_logs');
    }
};
