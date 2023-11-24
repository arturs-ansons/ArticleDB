<?php

declare(strict_types=1);

namespace App;

use App\Models\Article;
use App\Models\ArticleCollection;
use PDO;
use Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class DatabaseService
{
    private PDO $db;
    private ?Environment $twig = null;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $dsn = $_ENV['DB_DSN'] ?? null;
        $user = $_ENV['DB_USER'] ?? null;
        $password = $_ENV['DB_PASSWORD'] ?? null;

        $this->db = new PDO($dsn, $user, $password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function createArticle(array $articleData): void
    {
        $statement = $this->db->prepare("
        INSERT INTO articles (title, image, link) 
        VALUES (:title, :image, :link)");

        $statement->execute([
            'title' => $articleData['title'],
            'image' => $articleData['image'],
            'link' => $articleData['link']
        ]);
    }

    public function deleteArticle(int $articleId): void
    {
        $statement = $this->db->prepare("
        DELETE FROM articles
        WHERE id = :id");

        $statement->execute(['id' => $articleId]);
    }

    public function updateArticleById(int $articleId, ?string $newTitle): ?Article
    {
        $statement = $this->db->prepare("
        UPDATE articles 
        SET title = :title 
        WHERE id = :id
    ");
        $statement->execute([
            'id' => $articleId,
            'title' => $newTitle]
        );

        $updatedStatement = $this->db->prepare("
        SELECT * FROM articles 
        WHERE id = :id");
        $updatedStatement->execute(['id' => $articleId]);

        $articleData = $updatedStatement->fetch(PDO::FETCH_ASSOC);

        return new Article(
            (int)$articleData['id'],
            $articleData['title'],
            $articleData['image'],
            $articleData['date'],
            $articleData['link']
        );
    }

    public function getAllArticles(): ArticleCollection
    {
        $statement = $this->db->query("SELECT * FROM articles");
        $articlesData = $statement->fetchAll(PDO::FETCH_ASSOC);

        $allArticles = new ArticleCollection();

        foreach ($articlesData as $articleData) {
            $article = new Article(
                (int)$articleData['id'],
                $articleData['title'],
                $articleData['image'],
                $articleData['date'],
                $articleData['link']
            );

            $allArticles->add($article);
        }

        return $allArticles;
    }
    public function getArticleById(int $articleId): ?Article
    {
        $statement = $this->db->prepare("
        SELECT * FROM articles 
        WHERE id = :id");
        $statement->execute(['id' => $articleId]);

        $articleData = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$articleData) {
            return null;
        }

        return new Article(
            (int)$articleData['id'],
            $articleData['title'],
            $articleData['image'],
            $articleData['date'],
            $articleData['link']
        );
    }

    public function getTwig(): Environment
    {
        if ($this->twig === null) {
            $loader = new FilesystemLoader(__DIR__ . '/Views');
            $this->twig = new Environment($loader);
        }

        return $this->twig;
    }
}
