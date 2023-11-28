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

        try {
            $this->db = new PDO($dsn, $user, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            exit("Database connection error");
        }
    }

    public function createArticle(Article $articleData): void
    {
        try {
            $statement = $this->db->prepare("
            INSERT INTO articles (title, image, link) 
            VALUES (:title, :image, :link)");

            $statement->execute([
                'title' => $articleData->getTitle(),
                'image' => $articleData->getImage(),
                'link' => $articleData->getLink()
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
        }
    }

    public function deleteArticle(int $articleId): void
    {
        try {
            $statement = $this->db->prepare("
            DELETE FROM articles
            WHERE id = :id");

            $statement->execute(['id' => $articleId]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
        }
    }

    public function updateArticleById(int $articleId, ?string $newTitle): bool
    {
        try {
            $statement = $this->db->prepare("
            UPDATE articles 
            SET title = :title 
            WHERE id = :id
        ");

            return $statement->execute([
                'id' => $articleId,
                'title' => $newTitle
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateLink(int $articleId, ?string $newTitle): bool
    {
        $newLink = $this->generateLink($newTitle);

        try {
            $statement = $this->db->prepare("
            UPDATE articles 
            SET link = :link 
            WHERE id = :id
        ");

            return $statement->execute([
                'id' => $articleId,
                'link' => $newLink,
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            return false;
        }
    }

    private function generateLink(string $title): string
    {
        return 'http://localhost:8000/article/' . str_replace(' ', '-', $title);
    }

    public function getAllArticles(): ArticleCollection
    {
        try {
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
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            return new ArticleCollection();
        }
    }

    public function getArticleByTitle(string $title): ?Article
    {
        try {
            $statement = $this->db->prepare("
            SELECT * FROM articles 
            WHERE title = :title");
            $statement->execute(['title' => $title]);

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
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            return null;
        }
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
