<?php

declare (strict_types=1);

namespace App\Service;

use App\Interfaces\GpxFilesInterface;
use http\Exception\InvalidArgumentException;
use phpGPX\Models\Stats;
use phpGPX\phpGPX;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TwohundredCouches\GpxMerger\GpxMerger;

class GpxHelper
{
    private const PUBLIC_FOLDER = __DIR__ . '/../../public/';

    private LoggerInterface $logger;

    private float $defaultCompression;

    public function __construct(LoggerInterface $logger, string $defaultCompression)
    {
        $this->logger = $logger;
        $this->defaultCompression = floatval($defaultCompression);
    }

    public function getStatsFromDirectory(string $directory): ?Stats
    {
        if (!str_starts_with($directory, '/')) {
            $directory = self::PUBLIC_FOLDER . $directory;
        }

        $fs = new Filesystem();

        if (!$fs->exists($directory)) {
            $this->logger->warning(sprintf('Directory %s not found', $directory));

            return null;
        }

        $finder = new Finder();

        $finder
            ->files()
            ->in($directory)
            ->depth(0)
            ->filter(static function (SplFileInfo $file) {
                return $file->isFile() && $file->getExtension() === 'gpx';
            });

        if (!$finder->hasResults()) {
            return null;
        }

        $stats = new Stats();

        foreach ($finder as $file) {
            $stats = $this->mergeStats($stats, $this->getStatsFromFile($file));
        }

        return $stats;
    }

    public function getStatsFromFile(SplFileInfo $file): Stats
    {
        if ($file->getExtension() !== 'gpx') {
            throw new InvalidArgumentException(sprintf('Expected gpx file, got %s', $file->getExtension()));
        }

        $gpx = new phpGPX();

        $stats = new Stats();

        $file = $gpx->load($file->getRealPath());

        foreach ($file->tracks as $track) {
            $stats = $this->mergeStats($stats, $track->stats);
        }

        return $stats;
    }

    public function mergeStats(Stats $statsA, Stats $statsB): Stats
    {
        $stats = new Stats();

        $stats->distance = $statsA->distance + $statsB->distance;

        if ($statsA->cumulativeElevationGain === null) {
            $statsA->cumulativeElevationGain = 0;
        }

        if ($statsB->cumulativeElevationGain === null) {
            $statsB->cumulativeElevationGain = 0;
        }

        $stats->cumulativeElevationGain = $statsA->cumulativeElevationGain + $statsB->cumulativeElevationGain;

        if ($statsA->cumulativeElevationLoss === null) {
            $statsA->cumulativeElevationLoss = 0;
        }

        if ($statsB->cumulativeElevationLoss === null) {
            $statsB->cumulativeElevationLoss = 0;
        }

        $stats->cumulativeElevationLoss = $statsA->cumulativeElevationLoss + $statsB->cumulativeElevationLoss;

        $stats->startedAt = $statsA->startedAt && $statsA->startedAt <= $statsB->startedAt ?
            $statsA->startedAt : $statsB->startedAt;

        $stats->finishedAt = $statsA->finishedAt && $statsA->finishedAt >= $statsB->finishedAt ?
            $statsA->finishedAt : $statsB->finishedAt;

        return $stats;
    }

    public function mergeFilesInDirectory(
        string $source,
        string $destination = self::PUBLIC_FOLDER,
        ?float $compression = null
    ): ?string
    {
        if ($compression === null) {
            $compression = $this->defaultCompression;
        }

        if (!str_starts_with($source, '/')) {
            $source = self::PUBLIC_FOLDER . $source;
        }
        if (!str_starts_with($destination, '/')) {
            $destination = self::PUBLIC_FOLDER . $destination;
        }


        $fs = new Filesystem();

        if (!$fs->exists($source)) {
            $this->logger->warning(sprintf('Directory %s not found', $source));

            return null;
        }

        $files = [];
        $finder = new Finder();

        $finder
            ->files()
            ->in($source)
            ->depth(0)
            ->filter(static function (SplFileInfo $file) {
                return $file->isFile() && $file->getExtension() === 'gpx';
            });

        if (!$finder->hasResults()) {
            return null;
        }

        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }

        return GpxMerger::merge($files, $destination, null, $compression);
    }

    /**
     * @return array<SplFileInfo>
     */
    public function findGpxFilesForEntity(GpxFilesInterface $gpxFilesEntity): array
    {
        return $this->findGpxFilesInDirectory($gpxFilesEntity->getRelativePathToGpxFiles());
    }

    /**
     * @return array<SplFileInfo>
     */
    public function findGpxFilesInDirectory(string $directory): array
    {
        if (!str_starts_with($directory, '/')) {
            $directory = self::PUBLIC_FOLDER . $directory;
        }

        $fs = new Filesystem();

        if (!$fs->exists($directory)) {
            $this->logger->warning(sprintf('Directory %s not found', $directory));

            return [];
        }

        $finder = new Finder();

        $finder
            ->files()
            ->in($directory)
            ->depth(0)
            ->filter(static function (SplFileInfo $file) {
                return $file->isFile()
                    && $file->getExtension() === 'gpx';
            });

        $files = [];

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }

}