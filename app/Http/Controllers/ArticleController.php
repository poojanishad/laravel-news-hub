<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Exception;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
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
