<?php

namespace App\Services;

use App\Models\Article;

class ArticleMetaService
{
    public function getFilters(): array
    {
        return [
            'sources' => $this->getSources(),
            'authors' => $this->getAuthors(),
            'categories' => $this->getCategories(),
        ];
    }

    protected function getSources()
    {
        return Article::query()
            ->whereNotNull('source')
            ->distinct()
            ->orderBy('source')
            ->pluck('source')
            ->values();
    }

    protected function getAuthors()
    {
        return Article::query()
            ->whereNotNull('author')
            ->distinct()
            ->orderBy('author')
            ->pluck('author')
            ->values();
    }

    protected function getCategories()
    {
        return Article::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values();
    }
}
