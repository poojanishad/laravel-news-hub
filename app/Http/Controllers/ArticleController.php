<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleFilterService;
use Illuminate\Http\Request;
use App\Services\ArticleMetaService;

class ArticleController extends Controller
{

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

    public function meta(ArticleMetaService $metaService)
    {
        return response()->json(
            $metaService->getFilters()
        );
    }

}
