<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['service_id', 'is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('service_id')->constrained()->nullOnDelete();
        });
        Schema::table('courriers', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('service_id')->constrained()->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->after('section_id')->constrained('users')->nullOnDelete();
            $table->index(['service_id', 'section_id']);
        });
        Schema::table('outgoing_courriers', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('service_id')->constrained()->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->after('section_id')->constrained('users')->nullOnDelete();
            $table->index(['service_id', 'section_id']);
        });
        Schema::table('routings', function (Blueprint $table) {
            $table->foreignId('section_destination_id')->nullable()->after('service_destination_id')->constrained('sections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('routings', fn (Blueprint $table) => $table->dropConstrainedForeignId('section_destination_id'));
        Schema::table('outgoing_courriers', function (Blueprint $table) { $table->dropIndex(['service_id', 'section_id']); $table->dropConstrainedForeignId('assigned_user_id'); $table->dropConstrainedForeignId('section_id'); });
        Schema::table('courriers', function (Blueprint $table) { $table->dropIndex(['service_id', 'section_id']); $table->dropConstrainedForeignId('assigned_user_id'); $table->dropConstrainedForeignId('section_id'); });
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('section_id'));
        Schema::dropIfExists('sections');
    }
};
