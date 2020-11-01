<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Notifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['email', 'sms']);
            $table->text('to');
            $table->text('from')->nullable();
            $table->text('subject')->nullable();
            $table->text('body');
            $table->enum('status', ['pending', 'failed', 'succeeded'])
                ->default('pending');
            $table->text('response')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');

    }
}
