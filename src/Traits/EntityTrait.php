<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EntityTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    protected ?int $id;

    public function getId(): ?int
    {
        return $this->id ?? null;
    }
}