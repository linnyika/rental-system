<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE maintenance_requests
            MODIFY COLUMN status ENUM(
                'pending',
                'approved',
                'rejected',
                'completed'
            ) DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE maintenance_requests
            MODIFY COLUMN status ENUM(
                'pending',
                'in_progress',
                'completed'
            ) DEFAULT 'pending'
        ");
    }
};