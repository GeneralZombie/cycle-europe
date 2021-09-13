<?php

namespace App\DataFixtures;

use App\Entity\Tour;
use App\Entity\TourCollection;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tourCollection = new TourCollection();
        $tourCollection->setTitle('Cycle Europe');
        $tourCollection->setSlug(TourCollection::CYCLE_EUROPE_SLUG);
        $tourCollection->setSubtitle('Dies ist eine Übersicht über meine bisherigen Touren.');

        $tours = new ArrayCollection($manager->getRepository(Tour::class)->findBy(['active' => true]));

        $tourCollection->setTours($tours);

        $manager->persist($tourCollection);

        $manager->flush();
    }
}
