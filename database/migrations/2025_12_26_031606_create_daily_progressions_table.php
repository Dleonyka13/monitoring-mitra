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
        Schema::create('daily_progressions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pcl_allocation_id');
            $table->string('respondent_name');
            $table->string('address');
            $table->string('long');
            $table->string('lat');
            $table->enum('status', ['Pending', 'Diterima', 'Ditolak'])->default('Pending');
            $table->timestamps();

            $table->foreign('pcl_allocation_id')
                ->references('id')
                ->on('pcl_allocations')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_progressions');
    }
};
