<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FunSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks for clean seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing data in reverse order of dependencies
        DB::table('audit_trails')->truncate();
        DB::table('notifications')->truncate();
        DB::table('reports')->truncate();
        DB::table('settings')->truncate();
        DB::table('daily_activity_logs')->truncate();
        DB::table('sessions')->truncate();
        DB::table('activity_logs')->truncate();
        DB::table('tasks')->truncate();
        DB::table('maintenance_requests')->truncate();
        DB::table('payments')->truncate();
        DB::table('tenants')->truncate();
        DB::table('units')->truncate();
        DB::table('properties')->truncate();
        DB::table('caretakers')->truncate();
        DB::table('admins')->truncate();
        DB::table('landlords')->truncate();
        DB::table('personal_access_tokens')->truncate();
        DB::table('sessions')->truncate();
        DB::table('password_reset_tokens')->truncate();
        DB::table('users')->truncate();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // =============================================
        // ORDER 1: USERS (No dependencies)
        // =============================================

        // Admin User
        $adminUser = DB::table('users')->insertGetId([
            'username' => 'rental_admin',
            'email' => 'admin@rental.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'phone' => '0712345678',
            'full_name' => 'Rental Admin',
            'role' => 'admin',
            'is_active' => true,
            'last_login' => null,
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Landlord User
        $landlordUser = DB::table('users')->insertGetId([
            'username' => 'demo_landlord',
            'email' => 'landlord@rental.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'phone' => '0723456789',
            'full_name' => 'Demo Landlord',
            'role' => 'landlord',
            'is_active' => true,
            'last_login' => null,
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Caretaker User
        $caretakerUser = DB::table('users')->insertGetId([
            'username' => 'demo_caretaker',
            'email' => 'caretaker@rental.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'phone' => '0745678901',
            'full_name' => 'Demo Caretaker',
            'role' => 'caretaker',
            'is_active' => true,
            'last_login' => null,
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Tenant User 1
        $tenantUser1 = DB::table('users')->insertGetId([
            'username' => 'demo_tenant1',
            'email' => 'tenant1@rental.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'phone' => '0734567890',
            'full_name' => 'Demo Tenant One',
            'role' => 'tenant',
            'is_active' => true,
            'last_login' => null,
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Tenant User 2
        $tenantUser2 = DB::table('users')->insertGetId([
            'username' => 'demo_tenant2',
            'email' => 'tenant2@rental.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'phone' => '0756789012',
            'full_name' => 'Demo Tenant Two',
            'role' => 'tenant',
            'is_active' => true,
            'last_login' => null,
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // =============================================
        // ORDER 2: ADMINS (Depends on users)
        // =============================================

        DB::table('admins')->insert([
            'user_id' => $adminUser,
            'admin_level' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // =============================================
        // ORDER 3: LANDLORDS (Depends on users)
        // =============================================

        DB::table('landlords')->insert([
            'user_id' => $landlordUser,
            'company_name' => 'Greenview Properties Ltd',
            'tax_id' => 'KRA-123456789',
            'address' => 'Nairobi, Kenya',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // =============================================
        // ORDER 4: CARETAKERS (Depends on users, landlords)
        // =============================================

        DB::table('caretakers')->insert([
            'user_id' => $caretakerUser,
            'landlord_id' => $landlordUser,
            'hire_date' => Carbon::now()->subMonths(6),
            'salary' => 15000.00,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // =============================================
        // ORDER 5: PROPERTIES (Depends on landlords)
        // =============================================

        $propertyId1 = DB::table('properties')->insertGetId([
            'landlord_id' => $landlordUser,
            'name' => 'Greenview Apartments',
            'address' => 'Kilimani, Nairobi, Kenya',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $propertyId2 = DB::table('properties')->insertGetId([
            'landlord_id' => $landlordUser,
            'name' => 'Sunset Villas',
            'address' => 'Westlands, Nairobi, Kenya',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // =============================================
        // ORDER 6: PROPERTY UNITS (Depends on properties)
        // =============================================

        // Units for Greenview Apartments
        $unit1Id = DB::table('units')->insertGetId([
            'property_id' => $propertyId1,
            'unit_number' => 'A1',
            'rent_amount' => 25000.00,
            'status' => 'occupied',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $unit2Id = DB::table('units')->insertGetId([
            'property_id' => $propertyId1,
            'unit_number' => 'A2',
            'rent_amount' => 30000.00,
            'status' => 'available',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $unit3Id = DB::table('units')->insertGetId([
            'property_id' => $propertyId1,
            'unit_number' => 'B1',
            'rent_amount' => 35000.00,
            'status' => 'maintenance',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Units for Sunset Villas
        $unit4Id = DB::table('units')->insertGetId([
            'property_id' => $propertyId2,
            'unit_number' => 'C1',
            'rent_amount' => 45000.00,
            'status' => 'available',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $unit5Id = DB::table('units')->insertGetId([
            'property_id' => $propertyId2,
            'unit_number' => 'C2',
            'rent_amount' => 50000.00,
            'status' => 'occupied',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // =============================================
        // ORDER 7: TENANTS (Depends on users, units)
        // =============================================

        // Tenant 1 (Occupying Unit A1)
        DB::table('tenants')->insert([
            'user_id' => $tenantUser1,
            'property_unit_id' => $unit1Id,
            'lease_start_date' => Carbon::now()->subMonths(6),
            'lease_end_date' => Carbon::now()->addMonths(6),
            'is_current_tenant' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Tenant 2 (Occupying Unit C2)
        DB::table('tenants')->insert([
            'user_id' => $tenantUser2,
            'property_unit_id' => $unit5Id,
            'lease_start_date' => Carbon::now()->subMonths(3),
            'lease_end_date' => Carbon::now()->addMonths(9),
            'is_current_tenant' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // =============================================
        // ORDER 8: PAYMENTS (Depends on tenants, caretakers)
        // =============================================

        // Payments for Tenant 1 (Unit A1)
        $paymentMonths = [1, 2, 3, 4, 5];

        foreach ($paymentMonths as $month) {
            $paymentDate = Carbon::now()->subMonths(6 - $month);
            $dueDate = Carbon::now()->subMonths(6 - $month)->addDays(5);
            $status = $month == 4 ? 'pending' : 'completed';

            DB::table('payments')->insert([
                'tenant_id' => $tenantUser1,
                'amount' => 25000.00,
                'payment_date' => $status == 'completed' ? $paymentDate : null,
                'due_date' => $dueDate,
                'payment_method' => $status == 'completed' ? 'M-Pesa' : null,
                'transaction_id' => $status == 'completed' ? 'MPESA' . rand(100000, 999999) : null,
                'status' => $status,
                'receipt_url' => $status == 'completed' ? '/receipts/' . rand(1000, 9999) . '.pdf' : null,
                'verified_by' => $status == 'completed' ? $caretakerUser : null,
                'verified_at' => $status == 'completed' ? Carbon::now() : null,
                'notes' => $status == 'completed' ? 'Rent payment for month ' . $month : null,
                'created_at' => $paymentDate,
                'updated_at' => $paymentDate,
            ]);
        }

        // Payment for Tenant 2 (Unit C2)
        DB::table('payments')->insert([
            'tenant_id' => $tenantUser2,
            'amount' => 50000.00,
            'payment_date' => Carbon::now()->subMonths(2),
            'due_date' => Carbon::now()->subMonths(2)->addDays(5),
            'payment_method' => 'Bank Transfer',
            'transaction_id' => 'BANK' . rand(100000, 999999),
            'status' => 'completed',
            'receipt_url' => '/receipts/' . rand(1000, 9999) . '.pdf',
            'verified_by' => $caretakerUser,
            'verified_at' => Carbon::now()->subMonths(2),
            'notes' => 'Rent payment',
            'created_at' => Carbon::now()->subMonths(2),
            'updated_at' => Carbon::now()->subMonths(2),
        ]);

        // =============================================
        // ORDER 9: MAINTENANCE REQUESTS
        // (Depends on tenants, units, landlords, caretakers)
        // =============================================

        // Maintenance Request 1 (Major - needs landlord approval)
        $mr1Id = DB::table('maintenance_requests')->insertGetId([
            'tenant_id' => $tenantUser1,
            'property_unit_id' => $unit1Id,
            'request_date' => Carbon::now()->subDays(5),
            'description' => 'Water leakage in the bathroom ceiling. It has been dripping for 3 days.',
            'priority' => 'high',
            'status' => 'approved',
            'is_major' => true,
            'landlord_approval_status' => 'approved',
            'landlord_approval_date' => Carbon::now()->subDays(3),
            'approved_by_landlord_id' => $landlordUser,
            'caretaker_assigned_id' => $caretakerUser,
            'assessment_notes' => 'Checked the bathroom ceiling. Need to fix the plumbing on the upper floor.',
            'assessed_by_caretaker_id' => $caretakerUser,
            'assessed_at' => Carbon::now()->subDays(4),
            'completion_date' => null,
            'resolution_notes' => null,
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(3),
        ]);

        // Maintenance Request 2 (Minor - no landlord approval needed)
        $mr2Id = DB::table('maintenance_requests')->insertGetId([
            'tenant_id' => $tenantUser2,
            'property_unit_id' => $unit5Id,
            'request_date' => Carbon::now()->subDays(2),
            'description' => 'The kitchen faucet is leaking and needs to be replaced.',
            'priority' => 'medium',
            'status' => 'in_progress',
            'is_major' => false,
            'landlord_approval_status' => 'pending',
            'landlord_approval_date' => null,
            'approved_by_landlord_id' => null,
            'caretaker_assigned_id' => $caretakerUser,
            'assessment_notes' => 'Replacement faucet has been ordered.',
            'assessed_by_caretaker_id' => $caretakerUser,
            'assessed_at' => Carbon::now()->subDays(1),
            'completion_date' => null,
            'resolution_notes' => null,
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        // Maintenance Request 3 (Pending landlord approval)
        $mr3Id = DB::table('maintenance_requests')->insertGetId([
            'tenant_id' => $tenantUser1,
            'property_unit_id' => $unit1Id,
            'request_date' => Carbon::now()->subDays(1),
            'description' => 'Electrical wiring needs to be updated. The circuit keeps tripping.',
            'priority' => 'emergency',
            'status' => 'submitted',
            'is_major' => true,
            'landlord_approval_status' => 'pending',
            'landlord_approval_date' => null,
            'approved_by_landlord_id' => null,
            'caretaker_assigned_id' => null,
            'assessment_notes' => null,
            'assessed_by_caretaker_id' => null,
            'assessed_at' => null,
            'completion_date' => null,
            'resolution_notes' => null,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        // =============================================
        // ORDER 10: TASKS (Depends on maintenance_requests, caretakers)
        // =============================================

        // Task for Maintenance Request 1
        DB::table('tasks')->insert([
            'maintenance_request_id' => $mr1Id,
            'assigned_to_caretaker_id' => $caretakerUser,
            'task_description' => 'Fix water leakage in bathroom ceiling',
            'status' => 'in_progress',
            'started_at' => Carbon::now()->subDays(2),
            'completed_at' => null,
            'completion_notes' => null,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // Task for Maintenance Request 2
        DB::table('tasks')->insert([
            'maintenance_request_id' => $mr2Id,
            'assigned_to_caretaker_id' => $caretakerUser,
            'task_description' => 'Replace kitchen faucet',
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
            'completion_notes' => null,
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // =============================================
        // ORDER 11: DAILY ACTIVITY LOGS (Depends on caretakers)
        // =============================================

        DB::table('daily_activity_logs')->insert([
            'caretaker_id' => $caretakerUser,
            'log_date' => Carbon::now()->subDays(1),
            'activities_performed' => "1. Inspected Unit A1 for water leakage\n2. Ordered replacement faucet for Unit C2\n3. Cleaned common areas\n4. Checked electrical systems",
            'notes' => 'All units are in good condition except the reported issues.',
            'submitted_at' => Carbon::now()->subDays(1),
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        DB::table('daily_activity_logs')->insert([
            'caretaker_id' => $caretakerUser,
            'log_date' => Carbon::now()->subDays(2),
            'activities_performed' => "1. Responded to maintenance requests\n2. Cleaned the garden\n3. Checked security systems\n4. Prepared rental payment report",
            'notes' => 'Security systems are functioning properly.',
            'submitted_at' => Carbon::now()->subDays(2),
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // =============================================
        // ORDER 12: SYSTEM SETTINGS (Depends on users for updated_by)
        // =============================================

        $settings = [
            ['setting_key' => 'company_name', 'setting_value' => 'Greenview Properties Ltd', 'description' => 'Company name'],
            ['setting_key' => 'company_email', 'setting_value' => 'info@greenviewproperties.com', 'description' => 'Company email'],
            ['setting_key' => 'company_phone', 'setting_value' => '+254700000000', 'description' => 'Company phone'],
            ['setting_key' => 'rent_due_day', 'setting_value' => '5', 'description' => 'Rent due day of the month'],
            ['setting_key' => 'late_fee_percentage', 'setting_value' => '5', 'description' => 'Late fee percentage'],
            ['setting_key' => 'maintenance_budget', 'setting_value' => '50000', 'description' => 'Monthly maintenance budget'],
            ['setting_key' => 'currency', 'setting_value' => 'KES', 'description' => 'Currency code'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert([
                'setting_key' => $setting['setting_key'],
                'setting_value' => $setting['setting_value'],
                'description' => $setting['description'],
                'updated_by' => $adminUser,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // =============================================
        // ORDER 13: SESSION LOGS (Depends on users)
        // =============================================

        DB::table('sessions')->insert([
            'user_id' => $tenantUser1,
            'session_token' => 'demo_token_' . uniqid(),
            'login_time' => Carbon::now()->subHours(2),
            'logout_time' => null,
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);

        DB::table('sessions')->insert([
            'user_id' => $caretakerUser,
            'session_token' => 'demo_token_' . uniqid(),
            'login_time' => Carbon::now()->subHours(5),
            'logout_time' => Carbon::now()->subHours(3),
            'ip_address' => '192.168.1.101',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0) AppleWebKit/605.1.15',
        ]);

        // =============================================
        // ORDER 14: ACTIVITY LOGS (Depends on users)
        // =============================================

        $activities = [
            ['user_id' => $tenantUser1, 'action' => 'rent_payment', 'entity_type' => 'payment', 'entity_id' => 1, 'details' => 'Paid rent amount 25000'],
            ['user_id' => $tenantUser1, 'action' => 'maintenance_request', 'entity_type' => 'maintenance_requests', 'entity_id' => $mr1Id, 'details' => 'Submitted water leakage request'],
            ['user_id' => $caretakerUser, 'action' => 'task_assigned', 'entity_type' => 'tasks', 'entity_id' => 1, 'details' => 'Assigned to fix water leakage'],
            ['user_id' => $landlordUser, 'action' => 'maintenance_approved', 'entity_type' => 'maintenance_requests', 'entity_id' => $mr1Id, 'details' => 'Approved major maintenance request'],
            ['user_id' => $adminUser, 'action' => 'system_config', 'entity_type' => 'settings', 'entity_id' => 1, 'details' => 'Updated settings'],
        ];

        foreach ($activities as $activity) {
            DB::table('activity_logs')->insert([
                'user_id' => $activity['user_id'],
                'action' => $activity['action'],
                'entity_type' => $activity['entity_type'],
                'entity_id' => $activity['entity_id'],
                'details' => $activity['details'],
                'ip_address' => '127.0.0.1',
                'created_at' => Carbon::now()->subDays(rand(0, 10)),
            ]);
        }

        // =============================================
        // ORDER 15: NOTIFICATIONS (Depends on users)
        // =============================================

        $notifications = [
            ['user_id' => $tenantUser1, 'title' => 'Rent Payment Confirmed', 'message' => 'Your rent payment of KES 25,000 for Unit A1 has been confirmed.', 'type' => 'payment', 'link' => '/payments'],
            ['user_id' => $tenantUser1, 'title' => 'Maintenance Request Approved', 'message' => 'Your water leakage maintenance request has been approved.', 'type' => 'maintenance', 'link' => '/maintenance'],
            ['user_id' => $caretakerUser, 'title' => 'New Task Assigned', 'message' => 'You have been assigned to fix water leakage in Unit A1.', 'type' => 'task', 'link' => '/tasks'],
            ['user_id' => $landlordUser, 'title' => 'Maintenance Request Pending', 'message' => 'A major maintenance request requires your approval.', 'type' => 'maintenance', 'link' => '/maintenance'],
        ];

        foreach ($notifications as $notification) {
            DB::table('notifications')->insert([
                'user_id' => $notification['user_id'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'type' => $notification['type'],
                'link' => $notification['link'],
                'is_read' => rand(0, 1) == 1,
                'read_at' => rand(0, 1) == 1 ? Carbon::now()->subHours(rand(1, 24)) : null,
                'created_at' => Carbon::now()->subHours(rand(1, 48)),
                'updated_at' => Carbon::now(),
            ]);
        }

        // =============================================
        // ORDER 16: REPORTS (Depends on users)
        // =============================================

        DB::table('reports')->insert([
            'report_type' => 'payment_summary',
            'generated_by' => $adminUser,
            'parameters' => json_encode(['month' => 'January', 'year' => '2024']),
            'file_path' => '/reports/payment_summary_jan_2024.pdf',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        DB::table('reports')->insert([
            'report_type' => 'maintenance_report',
            'generated_by' => $landlordUser,
            'parameters' => json_encode(['status' => 'completed', 'date_from' => '2024-01-01', 'date_to' => '2024-01-31']),
            'file_path' => '/reports/maintenance_report_jan_2024.pdf',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        // =============================================
        // ORDER 17: AUDIT TRAILS (Depends on users)
        // =============================================

        $auditTrails = [
            ['user_id' => $adminUser, 'action' => 'create', 'table_name' => 'users', 'record_id' => $adminUser, 'old_values' => null, 'new_values' => json_encode(['email' => 'admin@rental.com', 'role' => 'admin'])],
            ['user_id' => $adminUser, 'action' => 'create', 'table_name' => 'landlords', 'record_id' => $landlordUser, 'old_values' => null, 'new_values' => json_encode(['company_name' => 'Greenview Properties Ltd'])],
            ['user_id' => $caretakerUser, 'action' => 'update', 'table_name' => 'maintenance_requests', 'record_id' => $mr1Id, 'old_values' => json_encode(['status' => 'submitted']), 'new_values' => json_encode(['status' => 'in_progress'])],
        ];

        foreach ($auditTrails as $audit) {
            DB::table('audit_trails')->insert([
                'user_id' => $audit['user_id'],
                'action' => $audit['action'],
                'table_name' => $audit['table_name'],
                'record_id' => $audit['record_id'],
                'old_values' => $audit['old_values'],
                'new_values' => $audit['new_values'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Laravel Seeder',
                'created_at' => Carbon::now()->subDays(rand(0, 5)),
                'updated_at' => Carbon::now()->subDays(rand(0, 5)),
            ]);
        }


        $this->command->info(' Login Credentials:');
        $this->command->info('Admin:      admin@rental.com     / password');
        $this->command->info('Landlord:   landlord@rental.com   / password');
        $this->command->info('Caretaker:  caretaker@rental.com  / password');
        $this->command->info('Tenant 1:   tenant1@rental.com    / password');
        $this->command->info('Tenant 2:   tenant2@rental.com    / password');
        $this->command->info('============================================');
    }
}
