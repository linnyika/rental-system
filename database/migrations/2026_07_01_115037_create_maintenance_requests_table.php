<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants', 'user_id')->onDelete('cascade');
            $table->foreignId('property_unit_id')->constrained('units')->onDelete('cascade');
            $table->timestamp('request_date')->useCurrent();
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'emergency'])->default('medium');
            $table->enum('status', ['submitted', 'assessed', 'approved', 'in_progress', 'completed', 'rejected'])->default('submitted');
            $table->boolean('is_major')->default(false);
            $table->enum('landlord_approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('landlord_approval_date')->nullable();
            $table->foreignId('approved_by_landlord_id')->nullable()->constrained('landlords', 'user_id')->onDelete('set null');
            $table->foreignId('caretaker_assigned_id')->nullable()->constrained('caretakers', 'user_id')->onDelete('set null');
            $table->text('assessment_notes')->nullable();
            $table->foreignId('assessed_by_caretaker_id')->nullable()->constrained('caretakers', 'user_id')->onDelete('set null');
            $table->timestamp('assessed_at')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('property_unit_id');
            $table->index('status');
            $table->index('priority');
            $table->index('landlord_approval_status');
            $table->index('caretaker_assigned_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
