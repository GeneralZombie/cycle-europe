<?php

namespace App\Twig;

use App\Service\CloudinaryHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CloudinaryExtension extends AbstractExtension
{
    private CloudinaryHelper $cloudinaryHelper;

    public function __construct(CloudinaryHelper $cloudinaryHelper)
    {
        $this->cloudinaryHelper = $cloudinaryHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cloudinary', [$this, 'cloudinary']),
        ];
    }

    public function cloudinary(string $src, ?int $width = null, ?int $height = null): string
    {
        return $this->cloudinaryHelper->getImageUrl($src, $width, $height);
    }
}
