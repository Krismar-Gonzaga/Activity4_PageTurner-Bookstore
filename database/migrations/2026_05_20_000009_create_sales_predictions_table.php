<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            // Predicted demand for the next period (30-day window)
            $table->integer('predicted_demand')->unsigned();
            // Current stock on hand
            $table->integer('current_stock')->unsigned();
            // Suggested reorder quantity
            $table->integer('suggested_reorder_qty')->unsigned();
            // Days until stock-out (based on predicted demand and current stock)
            $table->integer('days_until_stockout')->nullable();
            // Lead time in days to replenish stock
            $table->integer('lead_time_days')->default(14);
            // Reorder point – threshold below which an order should be placed now
            $table->integer('reorder_point')->unsigned();
            // Confidence level 0-100 (higher = more reliable prediction)
            $table->unsignedTinyInteger('confidence')->default(75);
            // Trigger status used by UI
            $table->enum('status', ['ok', 'watch', 'reorder_now', 'critical'])
                  ->default('ok');
            // Period start/end dates this prediction covers
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->timestamps();

            $table->unique(['book_id']);
            $table->index('status');
            $table->index('book_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_predictions');
    }
};
