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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
        $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
        $table->foreignId('landlord_id')->constrained()->cascadeOnDelete();
        $table->decimal('amount', 10, 2);
        $table->string('mpesa_reference')->unique();
        $table->date('payment_date');
        $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
       $table->foreignId('verified_by_caretaker')->nullable()->constrained('caretakers')->nullOnDelete();
       $table->foreignId('verified_by_landlord')->nullable()->constrained('landlords')->nullOnDelete();
        $table->timestamp('verified_at')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
