<?php

namespace App\Entity;

use App\Interfaces\GpxFilesInterface;
use App\Interfaces\SanityCheckInterface;
use App\Interfaces\StatsInterface;
use App\Repository\TourRepository;
use App\Traits\EntityTrait;
use App\Traits\PublishableTrait;
use App\Traits\StatsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourRepository::class)]
class Tour implements StatsInterface, GpxFilesInterface, SanityCheckInterface
{
    use EntityTrait;

    use PublishableTrait;

    use StatsTrait;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    #[ORM\Column(type: "boolean")]
    private bool $hideInList = false;

    #[ORM\Column(type: "array", nullable: true)]
    private $images = [];

    public function getDescription(): ?string
    {
        return $this->description ?? null;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getHideInList(): bool
    {
        return $this->hideInList;
    }

    public function setHideInList(bool $hideInList): void
    {
        $this->hideInList = $hideInList;
    }


    public function getImages(): ?array
    {
        return $this->images ?? null;
    }

    public function setImages(?array $images): void
    {
        $this->images = $images;
    }

    public function getHighlightImage(): ?string
    {
        return reset($this->images) ?: null;
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? $this->getId() ?? 'New Tour';
    }

    public static function getRelativeBasePathToGpxFiles(): string
    {
        return 'gpx/tour/';
    }

    public function getRelativePathToGpxFiles(): string
    {
        return self::getRelativeBasePathToGpxFiles() . $this->getSlug() . '/';
    }
}
