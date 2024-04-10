<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait StartedAtAndFinishedAtTrait
{
    #[ORM\Column(type: "date", nullable: true)]
    protected ?\DateTimeInterface $startedAt;

    #[ORM\Column(type: "date", nullable: true)]
    protected ?\DateTimeInterface $finishedAt;

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

    public function getDurationInDays(): int
    {
        if (!$this->getStartedAt() || !$this->getFinishedAt()) {
            return 0;
        }

        return date_diff($this->getStartedAt(), $this->getFinishedAt())->days + 1;
    }
}