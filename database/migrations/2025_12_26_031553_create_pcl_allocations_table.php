<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pcl_allocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('pml_allocation_id');
            $table->uuid('statistical_activity_id');
            $table->integer('target');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('pml_allocation_id')->references('id')->on('pml_allocations')->cascadeOnDelete();
            $table->foreign('statistical_activity_id')->references('id')->on('statistical_activities')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pcl_allocations');
    }
};
