<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use App\Services\PreferenceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class UserPreferenceController extends Controller
{
    public function __construct(
        private PreferenceService $service
    ) {}

    #[OA\Post(
        path: "/api/preferences",
        summary: "Store user preferences",
        tags: ["Preferences"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
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
                description: "Preferences saved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Preference saved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "user_id", type: "integer", example: 1),
                                new OA\Property(
                                    property: "sources",
                                    type: "array",
                                    items: new OA\Items(type: "string")
                                ),
                                new OA\Property(
                                    property: "categories",
                                    type: "array",
                                    items: new OA\Items(type: "string")
                                ),
                                new OA\Property(
                                    property: "authors",
                                    type: "array",
                                    items: new OA\Items(type: "string")
                                ),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            ),
            new OA\Response(
                response: 401,
                description: "User not resolved"
            )
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $user = $request->attributes->get('resolved_user');

        $validated = $request->validate([
            'sources' => ['nullable', 'array'],
            'categories' => ['nullable', 'array'],
            'authors' => ['nullable', 'array'],
        ]);

        $preference = $this->service->save($user, $validated);

        return response()->json([
            'message' => 'Preference saved successfully',
            'data' => $preference
        ]);
    }


    #[OA\Delete(
        path: "/api/preferences",
        summary: "Clear user preferences",
        tags: ["Preferences"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Preferences cleared successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Preference cleared successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "User not resolved"
            )
        ]
    )]
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->attributes->get('resolved_user');

        $this->service->clear($user);

        return response()->json([
            'message' => 'Preference cleared successfully'
        ]);
    }

    #[OA\Get(
        path: "/api/preferences",
        summary: "Get user preferences",
        tags: ["Preferences"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Preference retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "object",
                            nullable: true,
                            properties: [
                                new OA\Property(property: "user_id", type: "integer", example: 1),
                                new OA\Property(
                                    property: "sources",
                                    type: "array",
                                    items: new OA\Items(type: "string")
                                ),
                                new OA\Property(
                                    property: "categories",
                                    type: "array",
                                    items: new OA\Items(type: "string")
                                ),
                                new OA\Property(
                                    property: "authors",
                                    type: "array",
                                    items: new OA\Items(type: "string")
                                ),
                            ]
                        )
                    ]
                )
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

        return response()->json([
            'data' => $this->service->get($user)
        ]);
    }
}