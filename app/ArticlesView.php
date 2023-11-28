<?php

declare(strict_types=1);

namespace App;

use App\Models\Article;
use App\Models\ArticleCollection;
use Twig\Environment;

class ArticlesView
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function renderIndex(ArticleCollection $allArticles): string
    {
        return $this->twig->render('index.twig', [
            'allArticles' => $allArticles
        ]);
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function renderSingleArticle(Article $singleArticle): string
    {
        return $this->twig->render('article.twig', [
            'singleArticle' => $singleArticle]);
    }


}
