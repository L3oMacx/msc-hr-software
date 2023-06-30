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
        Schema::create('user_data_keys', function (Blueprint $table) {
            $table->id();
            $table->string('data_key', 255)->unique();
            $table->string('friendly_name', 255);
            $table->string('category', 255)->nullable();
            $table->string('value_type', 255)->default('text'); //text, select, int, float, bool
            $table->tinyInteger('encrypted')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_data_keys');
    }
};
