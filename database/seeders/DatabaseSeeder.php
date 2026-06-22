<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Landlord;
use App\Models\Caretaker;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use App\Models\TenantOccupancy;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =========================
        // ADMIN USER
        // =========================
        User::create([
                'name' => 'Rental Admin',
                'phone' => '0712345678',
                'email' => 'admin@rental.com',
                'role' => 'admin',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        // =========================
        // LANDLORD
        // =========================
            $landlordUser = User::create([
                'name' => 'Demo Landlord',
                'phone' => '0723456789',
                'email' => 'landlord@rental.com',
                'role' => 'landlord',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $landlord = Landlord::create([
            'user_id' => $landlordUser->id,
        ]);
        // =========================
        // CARETAKER
        // =========================
            $caretakerUser = User::create([
                'name' => 'Demo Caretaker',
                'phone' => '0745678901',
                'email' => 'caretaker@rental.com',
                'role' => 'caretaker',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $caretaker = Caretaker::create([
            'user_id' => $caretakerUser->id,
            'landlord_id' => $landlord->id,
        ]);
        // =========================
        // PROPERTY
        // =========================
        $property = $landlord->properties()->create([
            'name' => 'Greenview Apartments',
            'address' => 'Kilimani, Nairobi',
            'caretaker_id' => $caretaker->id,
        ]);
        // =========================
        // UNIT
        // =========================
        $unit = $property->units()->create([
            'unit_number' => 'A1',
            'rent_amount' => 25000,
            'is_occupied' => true,
        ]);
        // =========================
        // TENANT
        // =========================
         $tenantUser = User::create([
                'name' => 'Demo Tenant',
                'phone' => '0734567890',
                'email' => 'tenant@rental.com',
                'role' => 'tenant',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $tenant = Tenant::create([
            'user_id' => $tenantUser->id,
        ]);
        // =========================
        // TENANT OCCUPANCY
        TenantOccupancy::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
            'start_date' => Carbon::now()->subDays(30),
            'end_date' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
