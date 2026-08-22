<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outgoing_courriers', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('objet');
            $table->string('destinataire');
            $table->string('organisme_destinataire');
            $table->string('adresse_destinataire')->nullable();
            $table->date('date_envoi');
            $table->string('mode_envoi');
            $table->string('reference_postale')->nullable();
            $table->string('statut')->default('brouillon');
            $table->string('priorite')->default('normale');
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('statut');
            $table->index('priorite');
            $table->index('date_envoi');
            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outgoing_courriers');
    }
};
