<?php

namespace App\Domain\Project\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use App\Domain\Project\Entity\Translation\ProjectTranslation;

#[\Doctrine\ORM\Mapping\Entity]
class Project
{
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }
    #[\Doctrine\ORM\Mapping\Id]
    #[\Doctrine\ORM\Mapping\GeneratedValue]
    #[\Doctrine\ORM\Mapping\Column]
    private int $id;

    #[\Doctrine\ORM\Mapping\Column(type: Types::STRING, length: 255)]
    private string $image;


    #[\Doctrine\ORM\Mapping\OneToMany(
        mappedBy: 'project',
        targetEntity: ProjectTranslation::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $translations;


    public function getId(): int
    {
        return $this->id;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }



    public function addTranslation(ProjectTranslation $translation): void
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setProject($this);
        }
    }

    public function removeTranslation(ProjectTranslation $translation): void
    {
        $this->translations->removeElement($translation);
    }

    public function getTranslation(string $locale): ?ProjectTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->getLocale() === $locale) {
                return $translation;
            }
        }

        return null;
    }
}
