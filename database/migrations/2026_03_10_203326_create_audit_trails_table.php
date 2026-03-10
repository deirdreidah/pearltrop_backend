<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creating the audit_logs table to track user actions and system events.
     */
    public function up(): void
    {
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id()->comment('Primary key for audit log entry');

            // === Actor / User Context ===
            $table->unsignedBigInteger('user_id')->nullable()
                ->comment('The user who performed the action (not constrained, for flexibility)');
            $table->string('user_name')->nullable()
                ->comment('Cached name of the user at time of action');

            // === Event Context ===
            $table->string('event_type', 100)
                ->comment('Type of action performed (CREATE, UPDATE, DELETE, LOGIN, ERROR, etc.)');
            $table->string('module', 100)->nullable()
                ->comment('The system module where the action occurred (e.g. Bookings, Routes, Buses)');
            $table->string('model_type')->nullable()
                ->comment('Eloquent model class related to the action, e.g. App\\Models\\Booking');
            $table->unsignedBigInteger('model_id')->nullable()
                ->comment('ID of the affected record, if applicable');

            // === Change Data ===
            $table->json('old_values')->nullable()
                ->comment('Previous data before the change (if applicable)');
            $table->json('new_values')->nullable()
                ->comment('New data after the change (if applicable)');

            // === Request / System Info ===
            $table->string('ip_address', 45)->nullable()
                ->comment('IP address of the user or system making the request');
            $table->string('user_agent')->nullable()
                ->comment('Browser or device agent string for identifying the client');
            $table->string('platform')->nullable()
                ->comment('Platform or interface used (Admin, POS, API, Web Portal)');
            $table->string('status', 50)->default('success')
                ->comment('Result of the action: success, failure, warning, etc.');
            $table->text('message')->nullable()
                ->comment('Optional human-readable message or note for this log entry');

            $table->timestamps();

            $table->comment('Tracks all system activities and changes performed by users or system processes.');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
