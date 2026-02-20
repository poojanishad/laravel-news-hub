<?php

namespace App\Services;

use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Builder;

class ArticleFilterService
{
    public function apply(Builder $query, $request, $user): Builder
    {
        $query
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('title', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->when($request->source, fn($q) =>
                $q->whereIn('source', explode(',', $request->source))
            )
            ->when($request->category, fn($q) =>
                $q->whereIn('category', explode(',', $request->category))
            )
            ->when($request->author, fn($q) =>
                $q->whereIn('author', explode(',', $request->author))
            )
            ->when($request->date, fn($q) =>
                $q->whereDate('published_at', $request->date)
            );

        // Apply user preferences if requested
        if ($request->boolean('preferences')) {

            $pref = UserPreference::where('user_id', $user->id)->first();

            if ($pref) {

                if (!empty($pref->sources)) {
                    $query->whereIn('source', $pref->sources);
                }

                if (!empty($pref->categories)) {
                    $query->whereIn('category', $pref->categories);
                }

                if (!empty($pref->authors)) {
                    $query->whereIn('author', $pref->authors);
                }
            }
        }

        return $query;
    }
}