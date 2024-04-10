<?php

namespace App\Entity;

use App\Repository\RecordDistanceRepository;
use App\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecordDistanceRepository::class)]
class RecordDistance extends Record
{
    use EntityTrait;

    public function getType(): string 
    {
        return Record::TYPE_DISTANCE;
    }
}
