<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type', 50);
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('parameters')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('report_type');
            $table->index('created_at');
            $table->index('generated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
