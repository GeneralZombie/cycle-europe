<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait PublishableTrait
{
    #[ORM\Column(type: "string", length: 255)]
    protected ?string $title;

    #[ORM\Column(type: "boolean")]
    protected bool $active = true;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $subtitle;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    protected ?string $slug;

    public function getTitle(): ?string
    {
        return $this->title ?? null;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getSlug(): ?string
    {
        return $this->slug ?? null;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle ?? null;
    }

    public function setSubtitle(?string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }
}