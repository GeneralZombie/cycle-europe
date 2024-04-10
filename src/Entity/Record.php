<?php

namespace App\Entity;

use App\Traits\DistanceAndElevationTrait;
use App\Repository\RecordRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecordRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"distance" = "RecordDistance", "altitude" = "RecordAltitude"})
 */
abstract class Record
{
    use DistanceAndElevationTrait;

    public const TYPE_ALTITUDE = 'altitude';
    public const TYPE_DISTANCE = 'distance';

    #[ORM\Column(type: "string", nullable: true)]
    protected ?string $title;

    #[ORM\Column(type: "date", nullable: true)]
    protected ?\DateTimeInterface $date;

    #[ORM\ManyToOne(targetEntity: Tour::class)]
    protected ?Tour $tour;

    abstract public function getType(): string;

    public function getTitle(): ?string 
    {
        return $this->title ?? null;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date ?? null;
    }

    public function setDate(?\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function setTour(?Tour $tour): void
    {
        $this->tour = $tour;
    }

    public function getTour(): ?Tour
    {
        return $this->tour ?? null;
    }
}
