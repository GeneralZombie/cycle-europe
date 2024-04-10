<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait StatsTrait
{
    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $startedAt;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $finishedAt;

    #[ORM\Column(type: "integer")]
    private int $distance = 0;

    #[ORM\Column(type: "integer")]
    private int $elevationGain = 0;

    #[ORM\Column(type: "integer")]
    private int $elevationLoss = 0;

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt ?? null;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt ?? null;
    }

    public function setFinishedAt(?\DateTimeInterface $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): void
    {
        $this->distance = $distance;
    }

    public function getElevationGain(): int
    {
        return $this->elevationGain;
    }

    public function setElevationGain(?int $elevationGain): void
    {
        $this->elevationGain = $elevationGain;
    }

    public function getElevationLoss(): int
    {
        return $this->elevationLoss;
    }

    public function setElevationLoss(?int $elevationLoss): void
    {
        $this->elevationLoss = $elevationLoss;
    }

    public function getDurationInDays(): int
    {
        if (!$this->getStartedAt() || !$this->getFinishedAt()) {
            return 0;
        }

        return date_diff($this->getStartedAt(), $this->getFinishedAt())->days + 1;
    }
}