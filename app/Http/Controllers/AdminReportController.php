<?php

namespace App\Http\Controllers;

use App\Models\Caretaker;
use App\Models\Landlord;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\TenantOccupancy;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AdminReportController extends Controller
{
    public function dashboard()
    {
        $stats = $this->overviewStats();
        $logoPath = public_path('System Logo.jpg');

        return view('admin.dashboard', [
            'stats' => $stats,
            'logoUrl' => file_exists($logoPath) ? asset('System Logo.jpg') : null,
            'recentRegistrations' => User::with(['landlord', 'caretaker', 'tenant'])
                ->latest()
                ->take(10)
                ->get(),
            'recentPayments' => Payment::with(['tenant.user', 'unit.property', 'landlord.user'])
                ->latest('payment_date')
                ->take(5)
                ->get(),
        ]);
    }

    public function landlords()
    {
        $landlords = Landlord::with(['user', 'properties.units', 'properties.caretaker.user'])
            ->get()
            ->sortBy(fn (Landlord $landlord) => strtolower($landlord->user?->name ?? ''))
            ->values();

        return view('admin.reports.landlords.index', compact('landlords'));
    }

    public function landlordShow(Landlord $landlord)
    {
        $landlord->load([
            'user',
            'properties.units.property',
            'properties.caretaker.user',
        ]);

        $properties = $landlord->properties->sortBy('name')->values();
        $units = $properties->flatMap(fn (Property $property) => $property->units)->values();
        $occupiedUnits = $units->where('is_occupied', true)->values();
        $vacantUnits = $units->where('is_occupied', false)->values();
        $payments = Payment::with(['tenant.user', 'unit.property', 'landlord.user'])
            ->where('landlord_id', $landlord->id)
            ->latest('payment_date')
            ->get();
        $verifiedPayments = $payments->where('status', 'verified')->values();
        $monthlyRevenueTrends = $this->monthlyTrends($verifiedPayments);
        $currentCycleExpected = $occupiedUnits->sum('rent_amount');
        $currentCycleCollected = $verifiedPayments
            ->filter(fn (Payment $payment) => $payment->payment_date?->isCurrentMonth())
            ->sum('amount');
        $tenants = Tenant::with(['user', 'occupancies.unit.property.landlord.user', 'payments'])
            ->whereHas('occupancies.unit.property', function ($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })
            ->get()
            ->unique('id')
            ->sortBy(fn (Tenant $tenant) => strtolower($tenant->user?->name ?? ''))
            ->values();
        $caretakers = Caretaker::with(['user', 'activityLogs'])
            ->where('landlord_id', $landlord->id)
            ->get()
            ->sortBy(fn (Caretaker $caretaker) => strtolower($caretaker->user?->name ?? ''))
            ->values();

        return view('admin.reports.landlords.show', [
            'landlord' => $landlord,
            'properties' => $properties,
            'units' => $units,
            'occupiedUnits' => $occupiedUnits,
            'vacantUnits' => $vacantUnits,
            'caretakers' => $caretakers,
            'tenants' => $tenants,
            'payments' => $payments,
            'verifiedPayments' => $verifiedPayments,
            'monthlyRevenueTrends' => $monthlyRevenueTrends,
            'financials' => [
                'expected' => $currentCycleExpected,
                'collected' => $currentCycleCollected,
                'outstanding' => max($currentCycleExpected - $currentCycleCollected, 0),
            ],
        ]);
    }

    public function tenants()
    {
        $tenants = Tenant::with(['user', 'occupancies.unit.property', 'payments.unit.property'])
            ->get()
            ->sortBy(fn (Tenant $tenant) => strtolower($tenant->user?->name ?? ''))
            ->values();

        return view('admin.reports.tenants.index', compact('tenants'));
    }

    public function tenantShow(Tenant $tenant)
    {
        $tenant->load(['user', 'occupancies.unit.property', 'payments.unit.property']);
        $currentOccupancy = $this->currentOccupancy($tenant);
        $currentUnit = $currentOccupancy?->unit;
        $payments = $tenant->payments->sortByDesc('payment_date')->values();
        $maintenanceRequests = MaintenanceRequest::with(['unit.property'])
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->get();
        $outstandingBalance = $tenant->payments
            ->whereIn('status', ['pending', 'rejected'])
            ->sum('amount');

        return view('admin.reports.tenants.show', [
            'tenant' => $tenant,
            'currentOccupancy' => $currentOccupancy,
            'currentUnit' => $currentUnit,
            'payments' => $payments,
            'maintenanceRequests' => $maintenanceRequests,
            'outstandingBalance' => $outstandingBalance,
            'leaseInfo' => $currentOccupancy ? [
                'property' => $currentUnit?->property?->name,
                'unit' => $currentUnit?->unit_number,
                'started_at' => $currentOccupancy->start_date,
                'landlord' => $currentUnit?->property?->landlord?->user?->name,
            ] : null,
        ]);
    }

    public function caretakers()
    {
        $caretakers = Caretaker::with(['user', 'landlord.user', 'properties.units.occupancies', 'activityLogs'])
            ->get()
            ->sortBy(fn (Caretaker $caretaker) => strtolower($caretaker->user?->name ?? ''))
            ->values();

        return view('admin.reports.caretakers.index', compact('caretakers'));
    }

    public function caretakerShow(Caretaker $caretaker)
    {
        $caretaker->load([
            'user',
            'landlord.user',
            'properties.units.property',
            'properties.units.occupancies.tenant.user',
            'activityLogs',
        ]);

        $properties = $caretaker->properties->sortBy('name')->values();
        $units = $properties->flatMap(fn (Property $property) => $property->units)->values();
        $tenants = Tenant::with(['user', 'occupancies.unit.property'])
            ->whereHas('occupancies.unit.property', function ($query) use ($caretaker) {
                $query->where('caretaker_id', $caretaker->id);
            })
            ->get()
            ->unique('id')
            ->sortBy(fn (Tenant $tenant) => strtolower($tenant->user?->name ?? ''))
            ->values();
        $tasks = Task::with(['request.unit.property', 'request.tenant.user'])
            ->where('caretaker_id', $caretaker->id)
            ->latest()
            ->get();

        return view('admin.reports.caretakers.show', [
            'caretaker' => $caretaker,
            'properties' => $properties,
            'units' => $units,
            'tenants' => $tenants,
            'tasks' => $tasks,
            'activityLogs' => $caretaker->activityLogs->sortByDesc('activity_date')->values(),
        ]);
    }

    public function properties()
    {
        $properties = Property::with(['landlord.user', 'caretaker.user', 'units'])
            ->get()
            ->sortBy(fn (Property $property) => strtolower($property->name ?? ''))
            ->values();

        return view('admin.reports.properties.index', compact('properties'));
    }

    public function propertyShow(Property $property)
    {
        $property->load(['landlord.user', 'caretaker.user', 'units.property']);
        $units = $property->units->sortBy('unit_number')->values();
        $occupiedUnits = $units->where('is_occupied', true)->values();
        $vacantUnits = $units->where('is_occupied', false)->values();
        $payments = Payment::with(['tenant.user', 'unit.property'])
            ->where('landlord_id', $property->landlord_id)
            ->whereIn('unit_id', $units->pluck('id'))
            ->latest('payment_date')
            ->get();
        $verifiedPayments = $payments->where('status', 'verified')->values();
        $monthlyRevenueTrends = $this->monthlyTrends($verifiedPayments);
        $currentExpected = $occupiedUnits->sum('rent_amount');
        $currentCollected = $verifiedPayments
            ->filter(fn (Payment $payment) => $payment->payment_date?->isCurrentMonth())
            ->sum('amount');
        $tenants = Tenant::with('user')
            ->whereHas('occupancies.unit', function ($query) use ($property) {
                $query->where('property_id', $property->id);
            })
            ->get()
            ->unique('id')
            ->sortBy(fn (Tenant $tenant) => strtolower($tenant->user?->name ?? ''))
            ->values();

        return view('admin.reports.properties.show', [
            'property' => $property,
            'units' => $units,
            'occupiedUnits' => $occupiedUnits,
            'vacantUnits' => $vacantUnits,
            'payments' => $payments,
            'verifiedPayments' => $verifiedPayments,
            'monthlyRevenueTrends' => $monthlyRevenueTrends,
            'tenants' => $tenants,
            'financials' => [
                'expected' => $currentExpected,
                'collected' => $currentCollected,
                'outstanding' => max($currentExpected - $currentCollected, 0),
            ],
        ]);
    }

    private function overviewStats(): array
    {
        $totalUnits = Unit::count();
        $occupiedUnits = Unit::where('is_occupied', true)->count();

        return [
            'total_users' => User::count(),
            'total_landlords' => Landlord::count(),
            'total_tenants' => Tenant::count(),
            'total_caretakers' => Caretaker::count(),
            'total_properties' => Property::count(),
            'total_units' => $totalUnits,
            'total_occupied_units' => $occupiedUnits,
            'total_vacant_units' => max($totalUnits - $occupiedUnits, 0),
            'total_active_leases' => TenantOccupancy::whereNull('end_date')->count(),
            'total_rent_collected' => Payment::where('status', 'verified')->sum('amount'),
            'total_pending_payments' => Payment::where('status', 'pending')->count(),
        ];
    }

    private function monthlyTrends(Collection $payments, int $months = 6): Collection
    {
        return collect(range($months - 1, 0))->map(function (int $offset) use ($payments) {
            $month = Carbon::now()->subMonths($offset)->startOfMonth();

            return [
                'label' => $month->format('M Y'),
                'total' => $payments
                    ->filter(fn (Payment $payment) => $payment->payment_date?->isSameMonth($month))
                    ->sum('amount'),
            ];
        });
    }

    private function currentOccupancy(Tenant $tenant): ?TenantOccupancy
    {
        return $tenant->occupancies
            ->whereNull('end_date')
            ->sortByDesc('start_date')
            ->first();
    }
}
