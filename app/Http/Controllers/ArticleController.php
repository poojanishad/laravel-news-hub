<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Exception;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index1(Request $request)
    {
        $query = Article::query()

            // 🔍 Search
            ->when($request->search, fn ($q) =>
                $q->where(fn ($query) =>
                    $query->where('title', 'like', "%{$request->search}%")
                          ->orWhere('description', 'like', "%{$request->search}%")
                )
            )

            // 📅 Date filter
            ->when($request->date, fn ($q) =>
                $q->whereDate('published_at', $request->date)
            )

            // ->when($request->category, fn ($q) =>
            //     $q->whereIn('category', explode(',', $request->category))
            // )

            // // 📰 Single source filter
            // ->when($request->source, fn ($q) =>
            //     $q->whereIn('source', explode(',', $request->source))
            // )

            // // 📰 Single author filter
            // ->when($request->authors, fn ($q) =>
            //     $q->whereIn('author', explode(',', $request->authors))
            // )

            // ⭐ USER PREFERENCES (MAIN REQUIREMENT)
            ->userPreferences(
                $request->sources,
                $request->categories,
                $request->authors,
                $request->search
            )

            ->latest('published_at');
                
            //->paginate(10);
                $sql = $query->toSql();
                $bindings = $query->getBindings();

                $fullSql = vsprintf(
                str_replace('?', "'%s'", $sql),
                $bindings
                );


        return response()->json(
            $query
                ->orderBy('published_at', 'desc')
                ->get()
        );
    }

    public function index(Request $request)
    {
        try {
            $query = Article::query()
                ->userPreferences(
                    $request->source,
                    $request->category,
                    $request->author,
                    $request->search,
                    $request->date
                );
            
            return response()->json(
                $query
                    ->orderByDesc('published_at')
                    ->get()
            );
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something Went Wrong! Please try again.',
                'errors' => $e->getMessage()
            ]);
        }
    }
}
