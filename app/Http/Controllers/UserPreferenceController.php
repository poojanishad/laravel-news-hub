<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use App\Services\PreferenceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class UserPreferenceController extends Controller
{
    #[OA\Post(
        path: "/api/preferences",
        summary: "Store user preferences",
        tags: ["Preferences"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Preferences saved"
            )
        ]
    )]
    public function store(Request $request, PreferenceService $service): JsonResponse
    {
        $user = $request->attributes->get('resolved_user');

        if (!$user) {
            return response()->json([
                'message' => 'User not resolved'
            ], 401);
        }

        $validated = $request->validate([
            'sources' => ['nullable', 'array'],
            'categories' => ['nullable', 'array'],
            'authors' => ['nullable', 'array'],
        ]);

        $preference = $service->save($user, [
            'sources' => $validated['sources'] ?? [],
            'categories' => $validated['categories'] ?? [],
            'authors' => $validated['authors'] ?? [],
        ]);

        return response()->json([
            'message' => 'Preference saved successfully',
            'data' => $preference
        ]);
    }

    /**
     * Clear Preference
     */
    public function destroy(Request $request, PreferenceService $service): JsonResponse
    {
        $user = $request->attributes->get('resolved_user');

        if (!$user) {
            return response()->json([
                'message' => 'User not resolved'
            ], 401);
        }

        $service->clear($user);

        return response()->json([
            'message' => 'Preference cleared successfully'
        ]);
    }

    /**
     * Show Preference
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->attributes->get('resolved_user');

        if (!$user) {
            return response()->json([
                'message' => 'User not resolved'
            ], 401);
        }

        $preference = UserPreference::where('user_id', $user->id)->first();

        return response()->json([
            'data' => $preference
        ]);
    }
}
