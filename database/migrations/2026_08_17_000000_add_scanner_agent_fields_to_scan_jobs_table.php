<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scan_jobs', function (Blueprint $table) {
            // Agent connection status: 'connected' | 'disconnected' | null
            $table->string('agent_status')->nullable()->after('status');

            // Scanner device metadata (from agent discovery)
            $table->string('scanner_name')->nullable()->after('agent_status');
            $table->string('scanner_manufacturer')->nullable()->after('scanner_name');
            $table->string('scanner_model')->nullable()->after('scanner_manufacturer');
            $table->string('scanner_connection')->nullable()->after('scanner_model');

            // Human-readable error message from the agent (never raw tokens)
            $table->text('error_message')->nullable()->after('scanner_connection');
        });
    }

    public function down(): void
    {
        Schema::table('scan_jobs', function (Blueprint $table) {
            $table->dropColumn(['agent_status', 'scanner_name', 'scanner_manufacturer', 'scanner_model', 'scanner_connection', 'error_message']);
        });
    }
};
