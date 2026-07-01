<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return a standardized JSON success response.
     */
    protected function successResponse($data = null, string $message = 'Success', int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return a standardized JSON error response.
     */
    protected function errorResponse(string $message, int $status = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Validate request data and return errors if any.
     * If validation fails, the response will be returned automatically.
     */
    protected function validateRequest(Request $request, array $rules, array $messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        return null; // Validation passed
    }

    /**
     * Check if the authenticated user has a specific role.
     * If not, abort with 403.
     */
    protected function ensureRole(Request $request, string|array $roles)
    {
        $user = $request->user();
        $roles = is_array($roles) ? $roles : [$roles];

        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Unauthorized – insufficient role.');
        }
    }

    /**
     * Helper to create a notification (for use by many controllers).
     * Override in child controllers if needed.
     */
    protected function createNotification(int $userId, string $title, string $message, string $type = 'system')
    {
        \App\Models\Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
        ]);
    }
}