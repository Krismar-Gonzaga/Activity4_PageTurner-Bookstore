<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('two_factor_authentications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('app'); // 'app' or 'email'
            $table->string('secret')->nullable();
            $table->json('recovery_codes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('two_factor_authentications');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_enabled', 'two_factor_type']);
        });
    }
};