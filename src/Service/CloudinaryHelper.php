<?php

namespace App\Service;

use App\Entity\Tour;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryHelper
{
    private const CLOUDINARY_TRANSFORMATION_URL = 'https://res.cloudinary.com/%s/image/upload/%s/%s';

    private const UPLOAD_PATH = 'bike/%s';

    private string $cloudinaryCloudName;

    private string $cloudinaryUrl;

    public function __construct(string $cloudinaryCloudName, string $cloudinaryUrl)
    {
        $this->cloudinaryCloudName = $cloudinaryCloudName;
        $this->cloudinaryUrl = $cloudinaryUrl;
    }

    /**
     * @return array<string>
     */
    public function getImagesForTour(Tour $tour): array
    {
        $cloudinary = new Cloudinary(new Configuration($this->cloudinaryUrl));

        $files = [];

        try {
            $assets = $cloudinary->adminApi()->assets([
                'type' => 'upload',
                'prefix' => sprintf(self::UPLOAD_PATH, $tour->getSlug()),
                'max_results' => 100
            ]);


            foreach ($assets as $asset) {
                foreach ($asset as $resource) {
                    if (is_array($resource) && array_key_exists('public_id', $resource) && array_key_exists('format', $resource)) {
                        $files[] = $resource['public_id'] . '.' . $resource['format'];
                    }
                }
            }
        } catch (\Exception $exception) {
            // TODO: Errorhandling
        }

        return $files;
    }

    public function getImageUrl(string $src, ?int $width = null, ?int $height = null): string
    {
        $cropMode = 'c_fill';

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
