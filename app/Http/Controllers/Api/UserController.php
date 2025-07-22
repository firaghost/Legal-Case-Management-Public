<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Search users by name or email. Used by chat user-picker.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $term = $request->input('search', '');

        $query = User::query();
        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }

        // Exclude the current authenticated user if any
        if ($request->user()) {
            $query->where('id', '!=', $request->user()->id);
        }

        $users = $query->limit(10)->get(['id', 'name', 'email']);

        return response()->json($users);
    }
}






