<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->after('department_id')->constrained('users')->nullOnDelete();
            $table->index('department_id');
        });

        Schema::table('routings', function (Blueprint $table) {
            $table->foreignId('recipient_user_id')->nullable()->after('service_destination_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('routings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recipient_user_id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['department_id']);
            $table->dropConstrainedForeignId('manager_id');
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
