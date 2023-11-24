<?php

declare(strict_types=1);

namespace App\Controllers;

use JetBrains\PhpStorm\NoReturn;

class ArticlesController extends BaseController
{
    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function index(): string
    {

        $allArticles = $this->dbService->getAllArticles();

        return $this->articlesView->renderIndex($allArticles);
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function viewArticle(array $params): string
    {

        $articleId = (int)($params['id'] ?? 0);

        if ($articleId === 0) {
            header("Location: /not-found");
            exit();
        }

        $article = $this->dbService->getArticleById($articleId);

        if (!$article) {

            header("Location: /not-found");
            exit();
        }

        return $this->articlesView->renderSingleArticle($article);
    }
    #[NoReturn] public function deleteArticle(array $params): void
    {
        $articleId = (int)($params['id'] ?? 0);

        if ($articleId === 0) {

            header("Location: /not-found");
            exit();
        }

        $this->dbService->deleteArticle($articleId);

        header("Location: /");
        exit();
    }
    #[NoReturn] public function updateArticle(): void
    {
        $articleId = (int)($_POST['updateId'] ?? 0);
        $title = $_POST['newTitle'] ?? '';

        $this->dbService->updateArticleById($articleId, $title);

        header("Location: /");
        exit();
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    #[NoReturn] public function insertArticle(): void
    {
        $articleData = [
            'title' => $_POST['title'] ?? '',
            'image' => $_POST['image'] ?? '',
            'link' => $_POST['link'] ?? '',
        ];
        $this->dbService->createArticle($articleData);

        header("Location: /");
        exit();
    }

}
