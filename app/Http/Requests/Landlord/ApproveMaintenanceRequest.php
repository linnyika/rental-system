<?php

namespace App\Http\Requests\Landlord;

use Illuminate\Foundation\Http\FormRequest;

class ApproveMaintenanceRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user()?->role === 'landlord';
	}

	public function rules(): array
	{
		return [
			'status' => ['required', 'in:approved,rejected'],
			'rejection_reason' => ['required_if:status,rejected', 'nullable', 'string', 'max:1000'],
		];
	}
}
