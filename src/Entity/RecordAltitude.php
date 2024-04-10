<?php

namespace App\Entity;

use App\Repository\RecordAltitudeRepository;
use App\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecordAltitudeRepository::class)]
class RecordAltitude extends Record
{
    use EntityTrait;

    public function getType(): string 
    {
        return Record::TYPE_ALTITUDE;
    }
}
