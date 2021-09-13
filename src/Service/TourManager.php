<?php

declare (strict_types=1);

namespace App\Service;

use App\Entity\Tour;
use App\Model\GpxTrack;
use App\Model\SanityCheckResult;
use Doctrine\ORM\EntityManagerInterface;
use phpGPX\Models\Stats;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class TourManager
{
    private const PUBLIC_FOLDER = __DIR__ . '/../../public/';

    private GpxHelper $gpxReader;

    private LoggerInterface $logger;

    private EntityManagerInterface $em;

    public function __construct(GpxHelper $gpxReader, LoggerInterface $logger, EntityManagerInterface $em)
    {
        $this->gpxReader = $gpxReader;
        $this->logger = $logger;
        $this->em = $em;
    }

    public function updateStats(Tour $tour, bool $force = false): Tour
    {
        $stats = $this->gpxReader->getStatsFromDirectory($tour->getRelativePathToGpxFiles());

        if ($stats) {
            $this->applyStatsToTour($tour, $stats, $force);
        }

        return $tour;
    }

    public function applyStatsToTour(Tour $tour, Stats $stats, bool $force = false): Tour
    {
        if ($force || !$tour->getDistance()) {
            $tour->setDistance(intval(round($stats->distance)));
        }

        if ($force || !$tour->getElevationGain()) {
            $tour->setElevationGain(intval(round($stats->cumulativeElevationGain)));
        }

        if ($force || !$tour->getElevationLoss()) {
            $tour->setElevationLoss(intval(round($stats->cumulativeElevationLoss)));
        }

        if ($force || !$tour->getStartedAt()) {
            $tour->setStartedAt($stats->startedAt);
        }

        if ($force || !$tour->getFinishedAt()) {
            $tour->setFinishedAt($stats->finishedAt);
        }

        return $tour;
    }

    public function removeTour(Tour $tour)
    {
        $fs = new Filesystem();

        $oldAbsolutePath = self::PUBLIC_FOLDER . $tour->getRelativePathToGpxFiles();
        $newAbsolutePath = self::PUBLIC_FOLDER . Tour::getRelativeBasePathToGpxFiles() . '__DELETED__' . $tour->getSlug();

        // Don't delete the directory, just rename it to be safe
        if ($fs->exists($oldAbsolutePath)) {
            $fs->rename($oldAbsolutePath, $newAbsolutePath);
        }
    }

    public function handleSlugChange(string $oldSlug, string $newSlug): void
    {
        $fs = new Filesystem();

        $oldPath = self::PUBLIC_FOLDER . Tour::getRelativeBasePathToGpxFiles() . $oldSlug;
        $newPath = self::PUBLIC_FOLDER . Tour::getRelativeBasePathToGpxFiles() . $newSlug;

        if ($fs->exists($oldPath)) {
            $fs->rename($oldPath, $newPath);
        }
    }


    /**
     * @return array<GpxTrack>
     */
    public function getGpxTracks(Tour $tour): array
    {
        $gpxTracks = [];

        $files = $this->gpxReader->findGpxFilesForEntity($tour);

        foreach ($files as $file) {
            $relativePath = $tour->getRelativePathToGpxFiles() . $file->getBasename();

            $gpxTracks[] = new GpxTrack($relativePath);
        }

        return $gpxTracks;
    }

    /**
     * @return \Iterator<SanityCheckResult>
     */
    public function sanityCheckAllTours(): \Iterator
    {
        $tours = $this->em->getRepository(Tour::class)->findAll();

        foreach ($tours as $tour) {
            yield $this->sanityCheck($tour);
        }
    }

    public function sanityCheck(Tour $tour): SanityCheckResult
    {
        $fs = new Filesystem();

        $result = new SanityCheckResult($tour);

        if (!$fs->exists(self::PUBLIC_FOLDER . $tour->getRelativePathToGpxFiles())) {
            $result->addError(new \Error('Directory missing'));
        }

        if (count($this->gpxReader->findGpxFilesForEntity($tour)) === 0) {
            $result->addError(new \Error('GPX Files missing'));
        }

        return $result;
    }
}