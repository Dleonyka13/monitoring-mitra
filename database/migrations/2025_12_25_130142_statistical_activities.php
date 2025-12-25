<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistical_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('total_target');
            $table->boolean('is_done')->default(false); // status selesai / belum
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistical_activities');
    }

};
