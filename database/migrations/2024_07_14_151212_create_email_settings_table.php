<?php

// database/migrations/xxxx_xx_xx_create_email_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organization_id');
            $table->string('host');
            $table->integer('port');
            $table->integer('imap_port')->nullable();
            $table->string('encryption');
            $table->string('username');
            $table->string('password');
            $table->text('dkim_private_key')->nullable();
            $table->text('dkim_public_key')->nullable();
            $table->string('dkim_selector')->nullable();
            $table->string('dkim_domain')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_settings');
    }
}
