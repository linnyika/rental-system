<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceRequestRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user()?->role === 'tenant';
	}

	public function rules(): array
	{
		return [
			'description' => ['required', 'string', 'min:10', 'max:2000'],
			'is_major' => ['sometimes', 'boolean'],
			'priority' => ['sometimes', 'in:low,medium,high,emergency'],
			'category' => ['sometimes', 'in:plumbing,electrical,structural,appliance,pest,security,other'],
			'subject' => ['sometimes', 'string', 'max:255'],
		];
	}
}
