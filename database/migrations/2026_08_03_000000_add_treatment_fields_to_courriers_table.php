<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courriers', function (Blueprint $table) {
            $table->text('traitement_commentaire')->nullable();
            $table->string('traitement_piece_jointe')->nullable();

            $table->foreignId('traite_par')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('traite_le')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('courriers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('traite_par');

            $table->dropColumn([
                'traitement_commentaire',
                'traitement_piece_jointe',
                'traite_le',
            ]);
        });
    }
};
