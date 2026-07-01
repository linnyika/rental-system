<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade');
            $table->foreignId('property_unit_id')->constrained('units')->onDelete('restrict');
            $table->date('lease_start_date');
            $table->date('lease_end_date');
            $table->boolean('is_current_tenant')->default(true);
            $table->timestamps();

            $table->index('property_unit_id');
            $table->index('is_current_tenant');
            $table->index(['lease_start_date', 'lease_end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
