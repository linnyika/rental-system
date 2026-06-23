<?php

namespace Database\Seeders;

use App\Models\Caretaker;
use App\Models\Landlord;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\TenantOccupancy;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReportSeeder extends Seeder
{
    /**
     * Seed realistic report data for the admin oversight dashboard.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $landlords = collect([
                $this->createUserWithProfile(
                    'landlord',
                    'Grace Wanjiku',
                    '0722001001',
                    'report.landlord.grace@example.com'
                ),
                $this->createUserWithProfile(
                    'landlord',
                    'Peter Otieno',
                    '0722001002',
                    'report.landlord.peter@example.com'
                ),
            ]);

            $caretakers = collect([
                $this->createUserWithProfile(
                    'caretaker',
                    'Samuel Mwangi',
                    '0722002001',
                    'report.caretaker.samuel@example.com',
                    ['landlord_id' => $landlords[0]->id]
                ),
                $this->createUserWithProfile(
                    'caretaker',
                    'Faith Achieng',
                    '0722002002',
                    'report.caretaker.faith@example.com',
                    ['landlord_id' => $landlords[0]->id]
                ),
                $this->createUserWithProfile(
                    'caretaker',
                    'Daniel Kiptoo',
                    '0722002003',
                    'report.caretaker.daniel@example.com',
                    ['landlord_id' => $landlords[1]->id]
                ),
            ]);

            $tenants = collect([
                $this->createUserWithProfile(
                    'tenant',
                    'Brian Kimani',
                    '0722003001',
                    'report.tenant.brian@example.com'
                ),
                $this->createUserWithProfile(
                    'tenant',
                    'Mariam Hassan',
                    '0722003002',
                    'report.tenant.mariam@example.com'
                ),
                $this->createUserWithProfile(
                    'tenant',
                    'Kevin Maina',
                    '0722003003',
                    'report.tenant.kevin@example.com'
                ),
                $this->createUserWithProfile(
                    'tenant',
                    'Lucy Njeri',
                    '0722003004',
                    'report.tenant.lucy@example.com'
                ),
            ]);

            $properties = collect();

            $properties->push($this->createPropertyWithUnits(
                $landlords[0],
                $caretakers->where('landlord_id', $landlords[0]->id)->values(),
                'Milele Heights',
                'Kilimani, Nairobi',
                ['A1' => 28000, 'A2' => 30000, 'A3' => 32000]
            ));

            $properties->push($this->createPropertyWithUnits(
                $landlords[1],
                $caretakers->where('landlord_id', $landlords[1]->id)->values(),
                'Sunrise Court',
                'Nyali, Mombasa',
                ['B1' => 22000, 'B2' => 24000]
            ));

            $units = $properties
                ->flatMap(fn (Property $property) => $property->units()->orderBy('unit_number')->get())
                ->values();

            $occupiedUnits = $units->take(4);

            $occupiedUnits->each(function (Unit $unit, int $index) use ($tenants): void {
                $tenant = $tenants[$index];

                TenantOccupancy::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'unit_id' => $unit->id,
                        'end_date' => null,
                    ],
                    [
                        'start_date' => Carbon::now()->subMonths(4)->startOfMonth(),
                    ]
                );

                $unit->update(['is_occupied' => true]);

                $this->createPaymentsForActiveTenant($tenant, $unit);
            });

            $units->slice(4)->each(fn (Unit $unit) => $unit->update(['is_occupied' => false]));
        });
    }

    private function createUserWithProfile(
        string $role,
        string $name,
        string $phone,
        string $email,
        array $profileAttributes = []
    ): Landlord|Caretaker|Tenant {
        $factoryAttributes = User::factory()->make([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'role' => $role,
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ])->getAttributes();

        $user = User::firstOrCreate(
            ['email' => $email],
            $factoryAttributes
        );

        $user->forceFill([
            'name' => $name,
            'phone' => $phone,
            'role' => $role,
            'email_verified_at' => $user->email_verified_at ?? Carbon::now(),
        ])->save();

        return match ($role) {
            'landlord' => Landlord::firstOrCreate(['user_id' => $user->id]),
            'caretaker' => Caretaker::firstOrCreate(
                ['user_id' => $user->id],
                ['landlord_id' => $profileAttributes['landlord_id']]
            ),
            'tenant' => Tenant::firstOrCreate(['user_id' => $user->id]),
        };
    }

    private function createPropertyWithUnits(
        Landlord $landlord,
        Collection $caretakers,
        string $name,
        string $address,
        array $units
    ): Property {
        $property = Property::firstOrCreate(
            [
                'landlord_id' => $landlord->id,
                'name' => $name,
            ],
            [
                'address' => $address,
                'caretaker_id' => $caretakers->first()?->id,
            ]
        );

        $property->update([
            'address' => $address,
            'caretaker_id' => $caretakers->random()->id,
        ]);

        foreach ($units as $unitNumber => $rentAmount) {
            Unit::firstOrCreate(
                [
                    'property_id' => $property->id,
                    'unit_number' => $unitNumber,
                ],
                [
                    'rent_amount' => $rentAmount,
                    'is_occupied' => false,
                ]
            );
        }

        return $property->refresh();
    }

    private function createPaymentsForActiveTenant(Tenant $tenant, Unit $unit): void
    {
        $landlord = $unit->property->landlord;
        $caretaker = $unit->property->caretaker;

        collect(range(3, 0))->each(function (int $monthsAgo) use ($tenant, $unit, $landlord, $caretaker): void {
            $paymentDate = Carbon::now()->subMonths($monthsAgo)->startOfMonth()->addDays(4);
            $status = $monthsAgo === 0 ? 'pending' : 'verified';

            Payment::firstOrCreate(
                [
                    'reference' => sprintf(
                        'RPT-%s-%s',
                        $tenant->id,
                        $paymentDate->format('Ym')
                    ),
                ],
                [
                    'tenant_id' => $tenant->id,
                    'unit_id' => $unit->id,
                    'landlord_id' => $landlord->id,
                    'amount' => $unit->rent_amount,
                    'method' => ['mpesa', 'bank', 'cash'][$monthsAgo % 3],
                    'payment_date' => $paymentDate,
                    'status' => $status,
                    'verified_by_caretaker' => $status === 'verified' ? $caretaker?->id : null,
                    'verified_by_landlord' => $status === 'verified' ? $landlord->id : null,
                    'verified_at' => $status === 'verified' ? $paymentDate->copy()->addDay() : null,
                ]
            );
        });
    }
}
