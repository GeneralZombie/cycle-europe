<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait StatsTrait
{
    use StartedAtAndFinishedAtTrait;
    use DistanceAndElevationTrait;
}