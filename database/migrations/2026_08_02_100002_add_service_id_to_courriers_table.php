<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courriers', function (Blueprint $table) {
            $table->foreignId('service_id')
                ->nullable()
                ->after('service_destinataire')
                ->constrained('services')
                ->nullOnDelete();

            $table->index(['service_id']);
        });
    }

    public function down(): void
    {
        Schema::table('courriers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
    }
};
