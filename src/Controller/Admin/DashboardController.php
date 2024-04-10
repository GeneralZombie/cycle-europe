<?php

namespace App\Controller\Admin;

use App\Entity\Tour;
use App\Entity\TourCollection;
use App\Entity\RecordAltitude;
use App\Entity\RecordDistance;
use App\Model\SanityCheckResult;
use App\Service\TourCollectionManager;
use App\Service\TourManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{

    private TourManager $tourManager;

    private TourCollectionManager $tourCollectionManager;

    public function __construct(TourManager $tourManager, TourCollectionManager $tourCollectionManager)
    {
        $this->tourManager = $tourManager;
        $this->tourCollectionManager = $tourCollectionManager;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $sanityCheckResultsForTourCollections = $this->tourCollectionManager->sanityCheckAllTourCollections();
        $sanityCheckResultsForTours = $this->tourManager->sanityCheckAllTours();

        $sanityCheckResultsWithErrors = [];

        foreach($sanityCheckResultsForTourCollections as $sanityCheckResult) {
            if (!$sanityCheckResult->allGood()) {
                $sanityCheckResultsWithErrors[] = $sanityCheckResult;
            }
        }
        foreach($sanityCheckResultsForTours as $sanityCheckResult) {
            if (!$sanityCheckResult->allGood()) {
                $sanityCheckResultsWithErrors[] = $sanityCheckResult;
            }
        }

        return $this->render(
            'admin/dashboard.html.twig', [
                'sanityCheckResultsWithErrors' => $sanityCheckResultsWithErrors
            ]
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Cycle Europe Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Touren', 'fas fa-list', Tour::class);
        yield MenuItem::linkToCrud('Tourensammlungen', 'fas fa-list', TourCollection::class);
        yield MenuItem::linkToCrud('Rekorde Distanz', 'fas fa-list', RecordDistance::class);
        yield MenuItem::linkToCrud('Rekorde Höhenmeter', 'fas fa-list', RecordAltitude::class);
    }
}
