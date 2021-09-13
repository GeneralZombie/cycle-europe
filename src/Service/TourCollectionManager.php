<?php

declare (strict_types=1);

namespace App\Service;

use App\Entity\Tour;
use App\Entity\TourCollection;
use App\Model\GpxTrack;
use App\Model\SanityCheckResult;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;

class TourCollectionManager
{
    private const PUBLIC_FOLDER = __DIR__ . '/../../public/';

    private GpxHelper $gpxReader;

    private EntityManagerInterface $em;

    private RouterInterface $router;

    private LoggerInterface $logger;

    public function __construct(
        GpxHelper              $gpxReader,
        EntityManagerInterface $em,
        RouterInterface        $router,
        LoggerInterface        $logger
    )
    {
        $this->gpxReader = $gpxReader;
        $this->em = $em;
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * @return \Iterator<TourCollection>
     */
    public function updateAllTourCollections(bool $andFlush = false): \Iterator
    {
        yield $this->updateTourCollection($this->findCycleEurope(), $andFlush);
    }

    public function updateTourCollection(TourCollection $tourCollection, bool $andFlush = false): TourCollection
    {
        switch ($tourCollection->getSlug()) {
            case TourCollection::CYCLE_EUROPE_SLUG:
                $this->updateCycleEurope($tourCollection, $andFlush);
                break;
        }
    }

    private function updateCycleEurope(TourCollection $cycleEurope, bool $andFlush = false)
    {
        /** @var Tour[] $tours */
        $tours = $this->em->getRepository(Tour::class)->findBy(['active' => true]);

        $cycleEurope->setTours(new ArrayCollection());

        foreach ($tours as $tour) {
            if ($tour->isActive()) {
                $cycleEurope->addTour($tour);
            }
        }

        $this->em->persist($cycleEurope);

        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function findCycleEurope(): TourCollection
    {
        $cycleEurope = $this->em->getRepository(TourCollection::class)->findOneBy(['slug' => TourCollection::CYCLE_EUROPE_SLUG]);

        if (!$cycleEurope) {
            throw new \RuntimeException('Cycle Europe Tour not found.');
        }

        return $cycleEurope;
    }

    /**
     * @return array<GpxTrack>
     */
    public function getGpxTracks(TourCollection $tourCollection): array
    {
        $gpxTracks = [];

        $fs = new Filesystem();

        /** @var Tour $tour */
        foreach ($tourCollection->getTours() as $tour) {
            $file = $tour->getSlug() . '.gpx';

            $relativePath = $tourCollection->getRelativePathToGpxFiles() . $file;
            $absolutePath = realpath(self::PUBLIC_FOLDER . $relativePath);

            if (!$fs->exists($absolutePath)) {
                $this->logger->alert(
                    sprintf('GPX File "%s" for tour collection "%s" missing', $absolutePath, $tourCollection->getSlug())
                );

                continue;
            }

            $href = $this->router->generate('show', ['slug' => $tour->getSlug()]);

            $gpxTracks[] = new GpxTrack($relativePath, $href, $tour->getTitle());
        }

        return $gpxTracks;
    }


    public function addTourToCollections(Tour $tour): void
    {
        if (!$tour->isActive()) {
            return;
        }

        /** @var TourCollection[] $tourCollections */
        $tourCollections = $this->em->getRepository(TourCollection::class)->findAll();

        foreach ($tourCollections as $tourCollection) {

            switch ($tourCollection->getSlug()) {

                case TourCollection::CYCLE_EUROPE_SLUG:
                    $tourCollection->addTour($tour);

                    $this->createGpxFileIfNotExists($tourCollection, $tour);

                    break;
            }

        }
    }

    public function updateTourInCollection(Tour $tour): void
    {
        /** @var TourCollection[] $tourCollections */
        $tourCollections = $this->em->getRepository(TourCollection::class)->findAll();

        foreach ($tourCollections as $tourCollection) {
            if (!$tour->isActive()) {
                $tourCollection->removeTour($tour);

                continue;
            }

            switch ($tourCollection->getSlug()) {
                case TourCollection::CYCLE_EUROPE_SLUG:
                    $tourCollection->addTour($tour);
                    break;
            }

            if ($tourCollection->getTours()->contains($tour)) {
                $this->createGpxFileIfNotExists($tourCollection, $tour);
            }
        }
    }

    public function removeTourFromCollections(Tour $tour): void
    {
        $fs = new Filesystem();

        /** @var TourCollection[] $tourCollections */
        $tourCollections = $this->em->getRepository(TourCollection::class)->findAll();

        foreach ($tourCollections as $tourCollection) {
            if ($tourCollection->getTours()->contains($tour)) {

                $absolutePath = self::PUBLIC_FOLDER . $tourCollection->getRelativePathToGpxFiles() . $tour->getSlug() . '.gpx';

                if ($fs->exists($absolutePath)) {
                    $fs->remove($absolutePath);
                }

                $tourCollection->removeTour($tour);
            }
        }
    }

    public function handleTourSlugChange(Tour $tour, string $oldSlug, string $newSlug): void
    {
        $fs = new Filesystem();

        /** @var TourCollection[] $tourCollections */
        $tourCollections = $this->em->getRepository(TourCollection::class)->findAll();

        foreach ($tourCollections as $tourCollection) {
            if ($tourCollection->getTours()->contains($tour)) {
                $oldPath = self::PUBLIC_FOLDER . $tourCollection->getRelativePathToGpxFiles() . $oldSlug . '.gpx';
                $newPath = self::PUBLIC_FOLDER . $tourCollection->getRelativePathToGpxFiles() . $newSlug . '.gpx';

                if ($fs->exists($oldPath)) {
                    $fs->rename($oldPath, $newPath);
                }
            }
        }
    }

    public function createGpxFileIfNotExists(TourCollection $tourCollection, Tour $tour)
    {
        $fs = new Filesystem();

        $destination = self::PUBLIC_FOLDER . $tourCollection->getRelativePathToGpxFileForTour($tour);

        if (!$fs->exists($destination)) {
            $source = self::PUBLIC_FOLDER . $tour->getRelativePathToGpxFiles();

            $this->gpxReader->mergeFilesInDirectory($source, $destination);
        }
    }

    /**
     * @return \Iterator<SanityCheckResult>
     */
    public function sanityCheckAllTourCollections(): \Iterator
    {
        $tourCollections = $this->em->getRepository(TourCollection::class)->findAll();

        foreach ($tourCollections as $tourCollection) {
            yield $this->sanityCheck($tourCollection);
        }
    }

    public function sanityCheck(TourCollection $tourCollection): SanityCheckResult
    {
        $fs = new Filesystem();

        $result = new SanityCheckResult($tourCollection);

        if (!$fs->exists(self::PUBLIC_FOLDER . $tourCollection->getRelativePathToGpxFiles())) {
            $result->addError(new \Error('Directory missing'));
        }

        if (count($this->gpxReader->findGpxFilesForEntity($tourCollection)) === 0) {
            $result->addError(new \Error('GPX Files missing'));
        }

        foreach ($tourCollection->getTours() as $tour) {
            if (!$fs->exists(self::PUBLIC_FOLDER . $tourCollection->getRelativePathToGpxFileForTour($tour))) {
                $result->addError(
                    new \Error(
                        sprintf('Missing file %s', $tourCollection->getRelativePathToGpxFileForTour($tour))
                    )
                );
            }
        }

        $existingFiles = $this->gpxReader->findGpxFilesForEntity($tourCollection);

        foreach ($existingFiles as $existingFile) {
            if (!$tourCollection->getTours()->filter(
                function (Tour $tour) use ($existingFile): bool {
                    return $tour->getSlug() === $existingFile->getFilenameWithoutExtension();
                }
            )->count() > 0) {
                $result->addError(
                    new \Error(
                        sprintf('Unused file %s', $tourCollection->getRelativePathToGpxFiles() . $existingFile->getFilename())
                    )
                );
            }
        }

        return $result;
    }
}