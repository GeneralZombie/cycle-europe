<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DateDiffExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('dateDiff', [$this, 'dateDiff']),
        ];
    }

    public function dateDiff(\DateTime $dateA, \DateTime $dateB): int
    {
        return date_diff($dateA, $dateB)->days + 1;
    }
}
