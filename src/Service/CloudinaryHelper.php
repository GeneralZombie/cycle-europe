<?php

namespace App\Service;

class CloudinaryHelper
{
    private const CLOUDINARY_TRANSFORMATION_URL = 'https://res.cloudinary.com/%s/image/upload/%s/%s';

    private string $cloudinaryCloudName;

    public function __construct(string $cloudinaryCloudName)
    {
        $this->cloudinaryCloudName = $cloudinaryCloudName;
    }

    public function getImageUrl(string $src, ?int $width = null, ?int $height = null): string
    {
        $cropMode = 'c_scale';

        $transformations = [];

        if ($width) {
            $transformations[] = 'w_' . $width;
        }

        if ($height) {
            $transformations[] = 'h_' . $height;
        }

        $transformations[] = $cropMode;

        return sprintf(
            self::CLOUDINARY_TRANSFORMATION_URL,
            $this->cloudinaryCloudName,
            implode(',', $transformations),
            $src
        );
    }
}
