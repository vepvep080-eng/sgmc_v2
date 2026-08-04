<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courriers', function (Blueprint $table) {
            $table->id();

            // Auto-generated reference like "COUR-2026-0001", assigned in Courrier::booted().
            $table->string('numero')->unique();

            $table->string('objet');
            $table->string('expediteur');
            $table->date('date_reception');
            $table->string('reference')->nullable();

            // 'normale' | 'urgente' | 'tres_urgente' — see Courrier::PRIORITES.
            $table->string('priorite')->default('normale');

            $table->string('type');
            $table->string('service_destinataire');
            $table->text('description')->nullable();

            // Relative path on the "public" disk, nullable — see Courrier::pieceJointeUrl().
            $table->string('piece_jointe')->nullable();

            // 'nouveau' | 'en_cours' | 'traite' | 'archive' — see Courrier::STATUTS.
            $table->string('statut')->default('nouveau');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['statut']);
            $table->index(['priorite']);
            $table->index(['date_reception']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courriers');
    }
};
