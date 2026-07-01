<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // -------------------- TENANT SUBMITS PAYMENT (API) --------------------
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:mpesa,bank',
            'reference' => 'required|string|unique:payments,reference',
            'payment_date' => 'required|date',
        ]);
        if ($validation) return $validation;

        $tenant = $request->user()->tenant;
        $occupancy = $tenant->occupancies()->whereNull('end_date')->latest()->first();
        if (!$occupancy) {
            return $this->errorResponse('You are not assigned to a unit.', 422);
        }

        $unit = $occupancy->unit;
        $landlordId = $unit->property->landlord_id;

        $payment = Payment::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
            'landlord_id' => $landlordId,
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
            'payment_date' => $request->payment_date,
            'status' => 'pending',
        ]);

        // Notify landlord
        $landlordUser = $unit->property->landlord?->user;
        if ($landlordUser) {
            $this->createNotification($landlordUser->id, 'Payment Submitted', 'A tenant submitted a payment.', 'payment');
        }

        return $this->successResponse($payment, 'Payment submitted', 201);
    }

    // -------------------- VERIFY PAYMENT (Landlord or Caretaker) --------------------
    public function verify(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $user = $request->user();

        if ($user->role === 'caretaker') {
            $caretaker = $user->caretaker;
            if ($payment->landlord_id !== $caretaker->landlord_id) {
                return $this->errorResponse('Not within your scope.', 403);
            }
            $payment->verified_by_caretaker = $caretaker->id;
        } elseif ($user->role === 'landlord') {
            $landlord = $user->landlord;
            if ($payment->landlord_id !== $landlord->id) {
                return $this->errorResponse('Not within your scope.', 403);
            }
            $payment->verified_by_landlord = $landlord->id;
        } else {
            return $this->errorResponse('Not authorized to verify.', 403);
        }

        if ($payment->status === 'verified') {
            return $this->errorResponse('Already verified.', 422);
        }

        $payment->status = 'verified';
        $payment->verified_at = now();
        $payment->save();

        // Notify tenant
        $tenantUser = $payment->tenant?->user;
        if ($tenantUser) {
            $this->createNotification($tenantUser->id, 'Payment Verified', 'Your payment has been verified.', 'payment');
        }

        return $this->successResponse($payment, 'Payment verified');
    }

    // -------------------- TENANT PAYMENT HISTORY (API) --------------------
    public function history(Request $request)
    {
        $tenant = $request->user()->tenant;
        $payments = $tenant->payments()->orderBy('payment_date', 'desc')->get();
        return $this->successResponse($payments);
    }

    // -------------------- STORE CASH PAYMENT (Landlord/Caretaker) --------------------
    public function storeCash(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'tenant_id' => 'required|exists:tenants,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);
        if ($validation) return $validation;

        $user = $request->user();
        $tenant = Tenant::findOrFail($request->tenant_id);
        $occupancy = $tenant->occupancies()->whereNull('end_date')->latest()->first();
        if (!$occupancy) {
            return $this->errorResponse('Tenant not assigned to a unit.', 422);
        }

        $unit = $occupancy->unit;
        $landlordId = $unit->property->landlord_id;

        // Scope check
        if ($user->role === 'caretaker') {
            if ($user->caretaker->landlord_id !== $landlordId) {
                return $this->errorResponse('Not within your scope.', 403);
            }
            $verifier = ['verified_by_caretaker' => $user->caretaker->id];
        } elseif ($user->role === 'landlord') {
            if ($user->landlord->id !== $landlordId) {
                return $this->errorResponse('Not within your scope.', 403);
            }
            $verifier = ['verified_by_landlord' => $user->landlord->id];
        } else {
            return $this->errorResponse('Unauthorized', 403);
        }

        $payment = Payment::create(array_merge([
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
            'landlord_id' => $landlordId,
            'amount' => $request->amount,
            'method' => 'cash',
            'reference' => null,
            'payment_date' => $request->payment_date,
            'status' => 'verified',
            'verified_at' => now(),
        ], $verifier));

        // Notify tenant
        $tenantUser = $tenant->user;
        if ($tenantUser) {
            $this->createNotification($tenantUser->id, 'Payment Received', 'Cash payment verified.', 'payment');
        }

        return $this->successResponse($payment, 'Cash payment recorded', 201);
    }

    // -------------------- DELETE PAYMENT (Landlord only) --------------------
    public function destroy(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $landlord = $request->user()->landlord;
        if (!$landlord || $payment->landlord_id !== $landlord->id) {
            return $this->errorResponse('Not within your scope.', 403);
        }

        if ($payment->status === 'verified') {
            return $this->errorResponse('Cannot delete verified payments.', 403);
        }

        $payment->delete();
        return $this->successResponse(null, 'Payment deleted.');
    }
}
