<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('landlords', 'user_id')->onDelete('cascade');
            $table->string('name', 100);
            $table->text('address');
            $table->timestamps();

            $table->index('landlord_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
