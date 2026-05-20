<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // AI-generated accessible audio description for the visually impaired
            if (! Schema::hasColumn('books', 'ai_audio_description')) {
                $table->longText('ai_audio_description')->nullable()->after('language');
            }
            // Timestamp of last AI description generation
            if (! Schema::hasColumn('books', 'ai_description_at')) {
                $table->timestamp('ai_description_at')->nullable()->after('ai_audio_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'ai_description_at')) {
                $table->dropColumn('ai_description_at');
            }
            if (Schema::hasColumn('books', 'ai_audio_description')) {
                $table->dropColumn('ai_audio_description');
            }
        });
    }
};
