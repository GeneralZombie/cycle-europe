<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait DistanceAndElevationTrait
{
    #[ORM\Column(type: "integer")]
    protected int $distance = 0;

    #[ORM\Column(type: "integer")]
    protected int $elevationGain = 0;

    #[ORM\Column(type: "integer")]
    protected int $elevationLoss = 0;

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
}