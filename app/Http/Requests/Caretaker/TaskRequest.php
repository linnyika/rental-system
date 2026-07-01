<?php

namespace App\Http\Requests\Caretaker;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
	public function authorize(): bool
	{
		$user = $this->user();
		return $user && in_array($user->role, ['caretaker', 'admin'], true);
	}

	public function rules(): array
	{
		return [
			'status' => ['required', 'in:assigned,in_progress,completed,cancelled,done'],
			'completion_notes' => ['nullable', 'string', 'max:2000'],
		];
	}
}
