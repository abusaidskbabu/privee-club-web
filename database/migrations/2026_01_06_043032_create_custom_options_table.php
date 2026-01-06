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
        Schema::create('custom_options', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('parent_id')->nullable();
            $table->string('name')->nullable();
            $table->string('value');
            $table->string('slug')->unique()->nullable();
            $table->string('serial')->nullable();
            $table->string('type')->nullable();
            $table->integer('status')->default(1);
            $table->string('ancestor_id')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_options');
    }
};
