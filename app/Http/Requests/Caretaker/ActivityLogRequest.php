<?php

namespace App\Http\Requests\Caretaker;

use Illuminate\Foundation\Http\FormRequest;

class ActivityLogRequest extends FormRequest
{
	public function authorize(): bool
	{
		$user = $this->user();
		return $user && in_array($user->role, ['caretaker', 'admin'], true);
	}

	public function rules(): array
	{
		return [
			'description' => ['required', 'string', 'max:1000'],
			'activity_date' => ['nullable', 'date'],
		];
	}
}
