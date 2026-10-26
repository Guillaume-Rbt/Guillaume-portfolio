<?php

namespace App\Domain\Project\Entity;

use Doctrine\DBAL\Types\Types;

#[\Doctrine\ORM\Mapping\Entity]
class Project
{

    #[\Doctrine\ORM\Mapping\Id]
    #[\Doctrine\ORM\Mapping\GeneratedValue]
    #[\Doctrine\ORM\Mapping\Column]
    private int $id;

    #[\Doctrine\ORM\Mapping\Column(type: Types::STRING, length: 255)]
    private string $title;

    #[\Doctrine\ORM\Mapping\Column(type: Types::JSON)]
    private array $content;

    #[\Doctrine\ORM\Mapping\Column(type: Types::STRING, length: 255)]
    private string $slug;

    
    #[\Doctrine\ORM\Mapping\Column(type: Types::TEXT)]
    private string $excerpt;

    public function setExcerpt(string $excerpt): self
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }


    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): array
    {
        return $this->content;
    }
}
