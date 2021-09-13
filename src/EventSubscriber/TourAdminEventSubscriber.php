<?php

declare (strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Tour;
use App\Service\TourCollectionManager;
use App\Service\TourManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TourAdminEventSubscriber implements EventSubscriberInterface
{
    private TourManager $tourManager;

    private TourCollectionManager $tourCollectionManager;

    private EntityManagerInterface $em;

    public function __construct(
        TourManager $tourManager,
        TourCollectionManager $tourCollectionManager,
        EntityManagerInterface $em
    )
    {
        $this->tourManager = $tourManager;
        $this->tourCollectionManager = $tourCollectionManager;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['onBeforeTourPersisted'],
            BeforeEntityUpdatedEvent::class => ['onBeforeTourUpdated'],
            BeforeEntityDeletedEvent::class => ['onBeforeTourDeleted'],
        ];
    }

    public function onBeforeTourPersisted(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Tour) {
            return;
        }

        $this->updateStats($entity);

        $this->tourCollectionManager->addTourToCollections($entity);
    }

    public function onBeforeTourDeleted(BeforeEntityDeletedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Tour) {
            return;
        }

        $this->tourCollectionManager->removeTourFromCollections($entity);

        $this->tourManager->removeTour($entity);
    }

    public function onBeforeTourUpdated(BeforeEntityPersistedEvent|BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Tour) {
            return;
        }

        $this->updateStats($entity);

        // rename folder and files if slug has changed
        if ($entity->getId()) {
            $oldSlug = $this->em->getRepository(Tour::class)->fetchSlugById($entity->getId());

            if ($oldSlug && $oldSlug !== $entity->getSlug()) {
                $this->tourManager->handleSlugChange($oldSlug, $entity->getSlug());
                $this->tourCollectionManager->handleTourSlugChange($entity, $oldSlug, $entity->getSlug());
            }
        }

        $this->tourCollectionManager->updateTourInCollection($entity);
    }

    private function updateStats(Tour $tour): void
    {
        // if tour has all stats already set we won't update, to keep manual override
        // for force override gpx update admin action can be used
        if (!$tour->getDistance() ||
            !$tour->getElevationGain() ||
            !$tour->getElevationLoss() ||
            !$tour->getStartedAt() ||
            !$tour->getFinishedAt()) {
            $this->tourManager->updateStats($tour);
        }
    }
}
