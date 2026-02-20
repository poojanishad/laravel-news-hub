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
        description: "Saves or updates user news preferences (sources, categories, authors).",
        tags: ["Preferences"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "sources",
                        type: "array",
                        items: new OA\Items(type: "string"),
                        example: ["guardian", "newsapi"]
                    ),
                    new OA\Property(
                        property: "categories",
                        type: "array",
                        items: new OA\Items(type: "string"),
                        example: ["technology", "business"]
                    ),
                    new OA\Property(
                        property: "authors",
                        type: "array",
                        items: new OA\Items(type: "string"),
                        example: ["John Doe"]
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Preferences saved successfully"
            ),
            new OA\Response(
                response: 401,
                description: "User not resolved"
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

    #[OA\Delete(
        path: "/api/preferences",
        summary: "Clear user preferences",
        description: "Removes all saved preferences for the authenticated user.",
        tags: ["Preferences"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Preferences cleared successfully"
            ),
            new OA\Response(
                response: 401,
                description: "User not resolved"
            )
        ]
    )]
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

    #[OA\Get(
        path: "/api/preferences",
        summary: "Get user preferences",
        description: "Returns saved preferences for the authenticated user.",
        tags: ["Preferences"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Preference retrieved successfully"
            ),
            new OA\Response(
                response: 401,
                description: "User not resolved"
            )
        ]
    )]
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