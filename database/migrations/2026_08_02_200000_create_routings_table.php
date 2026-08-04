<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('courrier_id')->constrained()->cascadeOnDelete();

            // Null source means the courrier is being sent for the very
            // first time, directly from the Bureau d'Ordre.
            $table->foreignId('service_source_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('service_destination_id')->constrained('services')->cascadeOnDelete();

            $table->foreignId('routed_by')->constrained('users')->cascadeOnDelete();

            $table->string('statut')->default('en_attente'); // en_attente | accepte | refuse

            $table->timestamp('sent_at');
            $table->timestamp('received_at')->nullable();
            $table->text('commentaire')->nullable();

            $table->timestamps();

            $table->index(['courrier_id']);
            $table->index(['service_destination_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routings');
    }
};
