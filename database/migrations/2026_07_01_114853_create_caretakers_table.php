<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caretakers', function (Blueprint $table) {
            // This is the correct way - user_id is the primary key
            $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade');

            $table->foreignId('landlord_id')->constrained('landlords', 'user_id')->onDelete('cascade');
            $table->date('hire_date')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->timestamps();

            $table->index('landlord_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caretakers');
    }
};
