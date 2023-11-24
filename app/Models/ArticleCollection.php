<?php

declare(strict_types=1);

namespace App\Models;

class ArticleCollection
{
    private array $articleCollection = [];

    public function add(Article $article): void
    {
        $id = $article->getId();

        if (!isset($this->articleCollection[$id])) {
            $this->articleCollection[$id] = $article;
        }
    }

    public function getArticleCollection(): array
    {
        return $this->articleCollection;
    }
}
