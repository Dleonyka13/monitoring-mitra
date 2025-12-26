<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pml_allocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('statistical_activity_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('statistical_activity_id')->references('id')->on('statistical_activities')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pml_allocations');
    }
};
