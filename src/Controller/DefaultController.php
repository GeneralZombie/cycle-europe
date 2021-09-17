<?php

namespace App\Controller;

use App\Entity\Tour;
use App\Model\GpxTrack;
use App\Service\TourCollectionManager;
use App\Service\TourManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route("/", name: "home")]
    public function index(
        EntityManagerInterface $entityManager,
        TourCollectionManager $tourCollectionManager
    ): Response
    {
        $tours = $entityManager->getRepository(Tour::class)->findBy(
            ['hideInList' => false],
            ['startedAt' => 'DESC']
        );

        $cycleEurope = $tourCollectionManager->findCycleEurope();

        $gpxTracks = $tourCollectionManager->getGpxTracks($cycleEurope);

        return $this->render('default/index.html.twig', [
            'cycleEurope' => $cycleEurope,
            'gpxTracks' => $gpxTracks,
            'tours' => $tours,
        ]);
    }

    #[Route("/tour/{slug}", name: "show")]
    public function show(EntityManagerInterface $entityManager, TourManager $tourManager, string $slug): Response
    {
        $tour = $entityManager->getRepository(Tour::class)->findOneBy(['slug' => $slug]);

      //  $gpxTracks = $tourManager->getGpxTracks($tour);

        $gpxTracks = [
            new GpxTrack('gpx/tour-collection/cycle-europe/' . $tour->getSlug() . '.gpx')
        ];

        return $this->render('default/show.html.twig', [
            'tour' => $tour,
            'gpxTracks' => $gpxTracks,
        ]);
    }

    #[Route("/about", name: "about")]
    public function about(): Response
    {
        return $this->render('default/about.html.twig');
    }
}
