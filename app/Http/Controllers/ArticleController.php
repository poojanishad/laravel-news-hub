<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleFilterService;
use Illuminate\Http\Request;
use App\Services\ArticleMetaService;
use OpenApi\Attributes as OA;

class ArticleController extends Controller
{
    #[OA\Get(
        path: "/api/articles",
        summary: "Get filtered articles",
        description: "Returns paginated list of articles filtered by source, author, category, search or date.",
        tags: ["Articles"],
        parameters: [
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: "technology")
            ),
            new OA\Parameter(
                name: "sources",
                in: "query",
                required: false,
                description: "Comma separated list of sources",
                schema: new OA\Schema(type: "string", example: "guardian,newsapi")
            ),
            new OA\Parameter(
                name: "authors",
                in: "query",
                required: false,
                description: "Comma separated list of authors",
                schema: new OA\Schema(type: "string", example: "John Doe,Jane Smith")
            ),
            new OA\Parameter(
                name: "categories",
                in: "query",
                required: false,
                description: "Comma separated list of categories",
                schema: new OA\Schema(type: "string", example: "technology,business")
            ),
            new OA\Parameter(
                name: "date",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", format: "date", example: "2026-02-20")
            ),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Paginated list of articles",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "current_page",
                            type: "integer",
                            example: 1
                        ),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "title", type: "string", example: "Breaking News"),
                                    new OA\Property(property: "description", type: "string"),
                                    new OA\Property(property: "author", type: "string", example: "John Doe"),
                                    new OA\Property(property: "source", type: "string", example: "guardian"),
                                    new OA\Property(property: "category", type: "string", example: "technology"),
                                    new OA\Property(property: "url", type: "string"),
                                    new OA\Property(property: "image_url", type: "string"),
                                    new OA\Property(property: "published_at", type: "string", format: "date-time"),
                                ]
                            )
                        ),
                        new OA\Property(property: "total", type: "integer", example: 120),
                        new OA\Property(property: "per_page", type: "integer", example: 10),
                        new OA\Property(property: "last_page", type: "integer", example: 12)
                    ]
                )
            )
        ]
    )]
    public function index(Request $request, ArticleFilterService $filterService)
    {
        $user = $request->attributes->get('resolved_user');

        $query = Article::query();

        $query = $filterService->apply($query, $request, $user);

        $perPage = $request->get('per_page', 10);

        return response()->json(
            $query->latest('published_at')->paginate($perPage)
        );
    }

    #[OA\Get(
        path: "/api/articles/meta",
        summary: "Get article filter metadata",
        description: "Returns available sources, authors and categories for filter dropdowns.",
        tags: ["Articles"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Filter metadata",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "sources",
                            type: "array",
                            items: new OA\Items(type: "string"),
                            example: ["guardian", "newsapi", "gnews"]
                        ),
                        new OA\Property(
                            property: "authors",
                            type: "array",
                            items: new OA\Items(type: "string"),
                            example: ["John Doe", "Jane Smith"]
                        ),
                        new OA\Property(
                            property: "categories",
                            type: "array",
                            items: new OA\Items(type: "string"),
                            example: ["technology", "business", "sports"]
                        ),
                    ]
                )
            )
        ]
    )]
    public function meta(ArticleMetaService $metaService)
    {
        return response()->json(
            $metaService->getFilters()
        );
    }
}