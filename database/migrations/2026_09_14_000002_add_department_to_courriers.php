<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Statut hiérarchique : en_attente_de_dispatch = le courrier a été envoyé
     * par le BO/Admin vers 1..N départements et attend le dispatch du chef
     * de département vers ses services. (Traçabilité : reçu / transmis / traité.)
     * + department_id : département courant du courrier (NULL = non routé).
     */
    public function up(): void
    {
        Schema::table('courriers', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('service_id')->constrained('departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('courriers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
