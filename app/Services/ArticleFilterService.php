<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ArticleFilterService
{
    public function apply(Builder $query, Request $request, $user = null): Builder
    {
        return $query

            ->when(
                $request->filled('search'),
                fn ($q) => $q->where(function ($sub) use ($request) {
                    $sub->where('title', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                })
            )
            ->when(
                $request->filled('sources'),
                fn ($q) => $q->whereIn(
                    'source',
                    array_filter(explode(',', $request->sources))
                )
            )
            ->when(
                $request->filled('categories'),
                fn ($q) => $q->whereIn(
                    'category',
                    array_filter(explode(',', $request->categories))
                )
            )
            ->when(
                $request->filled('authors'),
                fn ($q) => $q->whereIn(
                    'author',
                    array_filter(explode(',', $request->authors))
                )
            )
            ->when(
                $request->filled('date'),
                fn ($q) => $q->whereDate('published_at', $request->date)
            );
    }
}