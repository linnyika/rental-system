<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Landlord;
use App\Models\Caretaker;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use App\Models\TenantOccupancy;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Landlord
        $landlordUser = User::create([
            'name' => 'Test Landlord',
            'phone' => '0700000001',
            'role' => 'landlord',
            'password' => 'secret123',
        ]);
        $landlord = Landlord::create(['user_id' => $landlordUser->id]);

        // Caretaker (tied to landlord)
        $caretakerUser = User::create([
            'name' => 'John Caretaker',
            'phone' => '0711111111',
            'role' => 'caretaker',
            'password' => 'secret123',
        ]);
        $caretaker = Caretaker::create([
            'user_id' => $caretakerUser->id,
            'landlord_id' => $landlord->id,
        ]);

        // Property + unit
        $property = $landlord->properties()->create([
            'name' => 'Greenview Apartments',
            'address' => 'Kilimani, Nairobi',
            'caretaker_id' => $caretaker->id,
        ]);
        $unit = $property->units()->create([
            'unit_number' => 'A1',
            'rent_amount' => 25000,
            'is_occupied' => true,
        ]);

        // Tenant (occupying the unit)
        $tenantUser = User::create([
            'name' => 'Mary Tenant',
            'phone' => '0722222222',
            'role' => 'tenant',
            'password' => 'secret123',
        ]);
        $tenant = Tenant::create(['user_id' => $tenantUser->id]);
        TenantOccupancy::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
            'start_date' => '2026-06-01',
        ]);
    }
}