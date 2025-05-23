<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('onesignal_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('push_notifications_id');
            $table->foreign('push_notifications_id')->references('id')->on('push_notifications')->onDelete('cascade');
            $table->uuid('onesignal_notification_id')->unique();
            $table->integer('sent')->default(0);
            $table->integer('delivered')->default(0);
            $table->integer('clicked')->default(0);
            $table->integer('dismissed')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('onesignal_notifications');
    }
};
