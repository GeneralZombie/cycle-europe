<?php

namespace App\Interfaces;

interface StatsInterface
{
    public function getStartedAt(): ?\DateTimeInterface;

    public function getFinishedAt(): ?\DateTimeInterface;

    public function getDistance(): int;

    public function getElevationGain(): int;

    public function getElevationLoss(): int;

    public function getDurationInDays(): int;
}