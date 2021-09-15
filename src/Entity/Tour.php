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
#[ORM\Table(name: 'tour')]
class Tour implements StatsInterface, GpxFilesInterface, SanityCheckInterface
{
    use EntityTrait;

    use PublishableTrait;

    use StatsTrait;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    #[ORM\Column(type: "boolean")]
    private bool $hideInList = false;

    #[ORM\Column(type: "string", nullable: true)]
    private string $poster;

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

    public function getPoster(): ?string
    {
        return $this->poster ?? null;
    }

    public function setPoster(?string $poster): void
    {
        $this->poster = $poster;
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
