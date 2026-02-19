<?php

namespace App\Services;

use App\Services\PreferenceService;


class ArticleFilterService
{
    protected $preferenceService;

    public function __construct(PreferenceService $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    public function apply($query, $request, $user)
    {
        $manualFilterUsed =
            $request->filled('search') ||
            $request->filled('source') ||
            $request->filled('category') ||
            $request->filled('date') ||
            $request->filled('author');

        if (!$manualFilterUsed && $user) {

            $pref = $this->preferenceService->getUserPreference($user);

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

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('author')) {
            $query->where('author', $request->author);
        }

        if ($request->filled('date')) {
            $start = $request->date . ' 00:00:00';
            $end   = $request->date . ' 23:59:59';

            $query->whereBetween('published_at', [$start, $end]);
        }

        return $query;
    }


    public function paginate($query, $perPage = 10)
    {
        return $query
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

}
