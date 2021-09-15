<?php

namespace App\Entity;

use App\Interfaces\GpxFilesInterface;
use App\Interfaces\SanityCheckInterface;
use App\Interfaces\StatsInterface;
use App\Repository\TourCollectionRepository;
use App\Traits\EntityTrait;
use App\Traits\PublishableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourCollectionRepository::class)]
#[ORM\Table(name: 'tour_collection')]
class TourCollection implements StatsInterface, GpxFilesInterface, SanityCheckInterface
{
    public const CYCLE_EUROPE_SLUG = 'cycle-europe';

    use EntityTrait;

    use PublishableTrait;

    #[ORM\ManyToMany(targetEntity: Tour::class)]
    private Collection $tours;

    public function __construct() {
        $this->tours = new ArrayCollection();
    }

    /**
     * @return Collection<Tour>
     */
    public function getTours(): Collection
    {
        return $this->tours;
    }

    public function setTours(Collection $tours): void
    {
        $this->tours = $tours;
    }

    public function addTour(Tour $tour): void
    {
        if (!$this->tours->contains($tour)) {
            $this->tours->add($tour);
        }
    }

    public function removeTour(Tour $tour): void
    {
        $this->tours->removeElement($tour);
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? $this->getId() ?? 'New Tour Collection';
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return null;
    }

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return null;
    }

    public function getDistance(): int
    {
        return array_reduce(
            $this->getTours()->toArray(),
            function (int $distance, Tour $tour): int {
                return $distance + $tour->getDistance();
            }, 0
        );
    }

    public function getElevationGain(): int
    {
        return array_reduce(
            $this->getTours()->toArray(),
            function ($elevationGain, Tour $tour): int {
                return $elevationGain + $tour->getElevationGain();
            }, 0
        );
    }

    public function getElevationLoss(): int
    {
        return array_reduce(
            $this->getTours()->toArray(),
            function (int $elevationLoss, Tour $tour): int {
                return $elevationLoss + $tour->getElevationLoss();
            }, 0
        );
    }

    public function getDurationInDays(): int
    {
        return array_reduce(
            $this->getTours()->toArray(),
            function (int $durationInDays, Tour $tour): int {
                return $durationInDays + $tour->getDurationInDays();
            }, 0
        );
    }

    public static function getRelativeBasePathToGpxFiles(): string
    {
        return 'gpx/tour-collection/';
    }

    public function getRelativePathToGpxFiles(): string
    {
        return self::getRelativeBasePathToGpxFiles() . $this->getSlug() . '/';
    }

    public function getRelativePathToGpxFileForTour(Tour $tour): string
    {
        return self::getRelativeBasePathToGpxFiles() . $this->getSlug() . '/' . $tour->getSlug() . '.gpx';
    }
}
