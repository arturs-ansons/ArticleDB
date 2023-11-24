<?php

declare(strict_types=1);

namespace App\Models;

class Article extends ArticleCollection
{
    private int $id;
    private string $title;
    private string $image;
    private string $dateCreated;
    private ?string $link;

    public function __construct(int $id, string $title, string $image, string $dateCreated, ?string $link)
    {
        $this->id = $id;
        $this->title = $title;
        $this->image = $image;
        $this->dateCreated = $dateCreated;
        $this->link = $link;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @throws \Exception
     */
    public function getDateCreated(): string
    {

            $dateCreated = new \DateTimeImmutable($this->dateCreated);

            return $dateCreated->format('Y-m-d H:i');
    }
    public function getLink(): ?string
    {
        return $this->link;
    }
}
