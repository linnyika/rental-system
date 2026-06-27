<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Notification;
class PaymentController extends Controller
{
   public function store(Request $request)
{
    $validated = $request->validate([
        'amount' => 'required|numeric|min:1',
        'method' => 'required|in:mpesa,bank',
        'reference' => 'required|string|unique:payments,reference',
        'payment_date' => 'required|date',
    ]);

    $tenant = $request->user()->tenant;

    // Find the tenant's current (open) occupancy → unit → landlord
    $occupancy = $tenant->occupancies()->whereNull('end_date')->latest()->first();

    if (! $occupancy) {
        return response()->json([
            'message' => 'You are not currently assigned to a unit.',
        ], 422);
    }

    $unit = $occupancy->unit;
    $landlordId = $unit->property->landlord_id;

    $payment = Payment::create([
        'tenant_id' => $tenant->id,
        'unit_id' => $unit->id,
        'landlord_id' => $landlordId,
        'amount' => $validated['amount'],
        'method' => $validated['method'],
        'reference' => $validated['reference'],
        'payment_date' => $validated['payment_date'],
        'status' => 'pending',
    ]);

    $landlordUser = $unit->property->landlord?->user;
    if ($landlordUser) {
        $this->createNotification(
            $landlordUser->id,
            'Payment Submitted',
            'A tenant has submitted a payment awaiting verification.',
            'payment'
        );
    }

    return response()->json([
        'message' => 'Payment submitted and pending verification',
        'payment' => $payment,
    ], 201);
} 
public function verify(Request $request, $paymentId)
{
    $user = $request->user();
    $payment = Payment::findOrFail($paymentId);

    // Scope check: the payment must belong to this verifier's landlord
    if ($user->role === 'caretaker') {
        $caretaker = $user->caretaker;
        if ($payment->landlord_id !== $caretaker->landlord_id) {
            return response()->json(['message' => 'This payment is not within your scope.'], 403);
        }
        $payment->verified_by_caretaker = $caretaker->id;
    } elseif ($user->role === 'landlord') {
        $landlord = $user->landlord;
        if ($payment->landlord_id !== $landlord->id) {
            return response()->json(['message' => 'This payment is not within your scope.'], 403);
        }
        $payment->verified_by_landlord = $landlord->id;
    } else {
        return response()->json(['message' => 'Not authorized to verify payments.'], 403);
    }

    if ($payment->status === 'verified') {
        return response()->json(['message' => 'This payment is already verified.'], 422);
    }

    $payment->status = 'verified';
    $payment->verified_at = now();
    $payment->save();

    $tenantUser = $payment->tenant?->user;
    if ($tenantUser) {
        $this->createNotification(
            $tenantUser->id,
            'Payment Verified',
            'Your payment has been verified successfully.',
            'payment'
        );
    }

    return response()->json([
        'message' => 'Payment verified successfully',
        'payment' => $payment,
    ]);
}
    public function history(Request $request)
{
    $tenant = $request->user()->tenant;

    $payments = $tenant->payments()
        ->orderBy('payment_date', 'desc')
        ->get();

    return response()->json([
        'payments' => $payments,
    ]);
}
public function storeCash(Request $request)
{
    $validated = $request->validate([
        'tenant_id' => 'required|exists:tenants,id',
        'amount' => 'required|numeric|min:1',
        'payment_date' => 'required|date',
    ]);

    $user = $request->user();

    // Find the tenant's current unit + landlord
    $tenant = \App\Models\Tenant::findOrFail($validated['tenant_id']);
    $occupancy = $tenant->occupancies()->whereNull('end_date')->latest()->first();

    if (! $occupancy) {
        return response()->json(['message' => 'This tenant is not assigned to a unit.'], 422);
    }

    $unit = $occupancy->unit;
    $landlordId = $unit->property->landlord_id;

    // Scope check: receiver must belong to this landlord
    if ($user->role === 'caretaker') {
        if ($user->caretaker->landlord_id !== $landlordId) {
            return response()->json(['message' => 'This tenant is not within your scope.'], 403);
        }
        $verifierColumn = ['verified_by_caretaker' => $user->caretaker->id];
    } else { // landlord
        if ($user->landlord->id !== $landlordId) {
            return response()->json(['message' => 'This tenant is not within your scope.'], 403);
        }
        $verifierColumn = ['verified_by_landlord' => $user->landlord->id];
    }

    $payment = Payment::create(array_merge([
        'tenant_id' => $tenant->id,
        'unit_id' => $unit->id,
        'landlord_id' => $landlordId,
        'amount' => $validated['amount'],
        'method' => 'cash',
        'reference' => null,
        'payment_date' => $validated['payment_date'],
        'status' => 'verified',
        'verified_at' => now(),
    ], $verifierColumn));

    $tenantUser = $tenant->user;
    if ($tenantUser) {
        $this->createNotification(
            $tenantUser->id,
            'Payment Verified',
            'Your payment has been verified successfully.',
            'payment'
        );
    }

    return response()->json([
        'message' => 'Cash payment recorded',
        'payment' => $payment,
    ], 201);
}
public function destroy(Request $request, $paymentId)
{
    $payment = Payment::findOrFail($paymentId);

    // scope to this landlord
    if ($payment->landlord_id !== $request->user()->landlord->id) {
        return response()->json(['message' => 'Not within your scope.'], 403);
    }

    // Protected record policy: cannot delete a verified payment
    if ($payment->status === 'verified') {
        return response()->json(['message' => 'Verified payments cannot be deleted.'], 403);
    }

    $payment->delete();
    return response()->json(['message' => 'Payment deleted.']);
}

private function createNotification(int $userId, string $title, string $message, string $type): void
{
    Notification::create([
        'user_id' => $userId,
        'title' => $title,
        'message' => $message,
        'type' => $type,
        'is_read' => false,
    ]);
}
}
