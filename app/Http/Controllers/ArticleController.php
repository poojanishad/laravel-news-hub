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
        tags: ["Articles"],
        parameters: [
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
                description: "List of articles"
            )
        ]
    )]
    public function index(Request $request, ArticleFilterService $filterService)
    {
        $query = Article::query();
        $user = $request->attributes->get('resolved_user');
        $query = $filterService->apply($query, $request, $user);
        $perPage = $request->get('per_page', 10);

        return response()->json(
            $filterService->paginate($query, $perPage)
        );
    }

    #[OA\Get(
        path: "/api/articles/meta",
        summary: "Get article filter metadata",
        tags: ["Articles"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Filter metadata"
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