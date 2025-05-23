<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invites', function (Blueprint $table) {
            // Remove columns
            $table->dropColumn(['team_id', 'invited_by', 'provider']);

            // Add new columns
            $table->string('action');
            $table->json('body');
            $table->foreignId('created_by')->constrained('users');

            $table->string('key', 36)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invites', function (Blueprint $table) {
            $table->string('key', 32)->change();

            // Add removed columns back
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('invited_by');
            $table->string('provider');

            // Remove new columns
            $table->dropColumn(['action', 'body', 'created_by']);
        });
    }
};
