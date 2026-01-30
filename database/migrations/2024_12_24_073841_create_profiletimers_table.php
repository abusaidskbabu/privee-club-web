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
        Schema::create('profiletimers', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('time')->nullable();
            $table->decimal('stickness_level', 5, 2)->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiletimers');
    }
};
