<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Generate financial summary for a landlord (or admin).
     */
    public function financialSummary(Request $request)
    {
        $user = $request->user();
        $landlordId = null;

        if ($user->role === 'landlord') {
            $landlordId = $user->landlord->id;
        } elseif ($user->role === 'admin') {
            // Admin may filter by landlord_id if provided
            $landlordId = $request->input('landlord_id');
        } else {
            return $this->errorResponse('Unauthorized', 403);
        }

        $query = Payment::query()->where('status', 'verified');

        if ($landlordId) {
            $query->where('landlord_id', $landlordId);
        }

        $totalCollected = $query->sum('amount');
        $totalCount = $query->count();
        $averagePayment = $totalCount > 0 ? $totalCollected / $totalCount : 0;

        // Monthly trends (last 6 months)
        $monthlyTrends = $query->select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        return $this->successResponse([
            'total_collected' => $totalCollected,
            'total_payments' => $totalCount,
            'average_payment' => round($averagePayment, 2),
            'monthly_trends' => $monthlyTrends,
        ]);
    }

    /**
     * Generate occupancy report.
     */
    public function occupancyReport(Request $request)
    {
        $user = $request->user();
        $landlordId = null;

        if ($user->role === 'landlord') {
            $landlordId = $user->landlord->id;
        } elseif ($user->role === 'admin') {
            $landlordId = $request->input('landlord_id');
        } else {
            return $this->errorResponse('Unauthorized', 403);
        }

        $propertiesQuery = Property::query();
        if ($landlordId) {
            $propertiesQuery->where('landlord_id', $landlordId);
        }

        $properties = $propertiesQuery->withCount([
            'units as total_units',
            'units as occupied_units' => function ($q) {
                $q->where('is_occupied', true);
            }
        ])->get();

        $overall = [
            'total_units' => $properties->sum('total_units'),
            'occupied_units' => $properties->sum('occupied_units'),
            'vacant_units' => $properties->sum('total_units') - $properties->sum('occupied_units'),
        ];

        return $this->successResponse([
            'properties' => $properties,
            'overall' => $overall,
        ]);
    }
}
