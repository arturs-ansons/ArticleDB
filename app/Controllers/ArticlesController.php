<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Article;
use App\DatabaseService;
use App\ArticlesView;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

class ArticlesController extends BaseController
{
    #[Pure] public function __construct(DatabaseService $dbService, ArticlesView $articlesView)
    {
        parent::__construct($dbService, $articlesView);
    }

    /**
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    public function index(): string
    {
        $allArticles = $this->dbService->getAllArticles();

        return $this->articlesView->renderIndex($allArticles);
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function viewArticle(array $params): string
    {
        $articleId = (string)($params['title'] ?? 0);

        $article = $this->dbService->getArticleByTitle($articleId);

        if (!$article) {
            header("Location: /not-found", true, 404);
            exit();
        }

        return $this->articlesView->renderSingleArticle($article);
    }

    #[NoReturn] public function deleteArticle(array $params): void
    {
        $articleId = (int)($params['id'] ?? 0);

        if ($articleId === 0) {
            header("Location: /not-found", true, 404);
            exit();
        }

        $this->dbService->deleteArticle($articleId);

        header("Location: /");
        exit();
    }

    #[NoReturn] public function updateArticle(): void
    {
        $articleId = (int)($_POST['updateId'] ?? 0);
        $newTitle = $_POST['newTitle'] ?? '';

        $this->dbService->updateArticleById($articleId, $newTitle);

        $this->dbService->updateLink($articleId, $newTitle);

        header("Location: /");
        exit();
    }

    #[NoReturn] public function insertArticle(): void
    {
        $title = $_POST['title'] ?? '';

        if ($this->dbService->getArticleByTitle($title) === null) {
            $articleData = new Article(
                0,
                $title,
                $_POST['image'] ?? '',
                "",
                $_POST['link'] ?? null
            );

            $this->dbService->createArticle($articleData);
            http_response_code(201);
            die();
        } else {
            $validationMessage = "An article with the same title already exists. Please choose another!";
            http_response_code(400);
            die(json_encode(['validationMessage' => $validationMessage]));
        }
    }
}
