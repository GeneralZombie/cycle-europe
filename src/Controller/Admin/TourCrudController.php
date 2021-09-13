<?php

namespace App\Controller\Admin;

use App\Admin\Field\ImageCloudField;
use App\Entity\Tour;
use App\Service\TourManager;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class TourCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tour::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort([
                'finishedAt' => 'DESC',
            ])
            ->setPaginatorPageSize(50)
            ->setEntityLabelInPlural('Touren');
    }

    public function configureActions(Actions $actions): Actions
    {
        $gpxAction = Action::new('updateStats', 'GPX Statistik updaten')
            ->linkToCrudAction('updateStats');

        return $actions
            ->add(Crud::PAGE_INDEX, $gpxAction)
            ->reorder(Crud::PAGE_INDEX, [Action::EDIT, 'updateStats', Action::DELETE]);
    }

    public function updateStats(AdminContext $context, TourManager $tourManager)
    {
        /** @var Tour $tour */
        $tour = $context->getEntity()->getInstance();

        $tourManager->updateStats($tour, true);
        $this->addFlash('success', sprintf('GPX Statistik von %s wurde aktualisiert.', $tour->getTitle()));

        return $this->redirect(
            $this->get(AdminUrlGenerator::class)
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl());
    }

    public function configureFields(string $pageName): iterable
    {
        yield ImageCloudField::new('highlightImage')
            ->setLabel('')
            ->onlyOnIndex();

        yield BooleanField::new('active')
            ->setLabel('Aktiv');

        yield BooleanField::new('hideInList')
            ->setLabel('In Liste ausblenden')
            ->hideOnIndex();

        yield TextField::new('title')
            ->setLabel('Titel');

        yield TextField::new('slug')
            ->setLabel('Slug');

        yield TextField::new('subtitle')
            ->setLabel('Untertitel')
            ->hideOnIndex();

        yield TextareaField::new('description')
            ->setLabel('Beschreibung')
            ->hideOnIndex();

        yield DateField::new('startedAt')
            ->setLabel('Von');

        yield DateField::new('finishedAt')
            ->setLabel('Bis');

        yield ArrayField::new('images')
            ->setLabel('Bilder')
            ->hideOnIndex();

        yield IntegerField::new('distance')
            ->setLabel('Distanz')
            ->setHelp('This value is calculated from gpx files. Erase it to trigger recalculation on save.')
            ->formatValue(function($value) {
                return intval(round($value / 1000)) . 'km';
            });

        yield IntegerField::new('elevationGain')
            ->setLabel('Höhenmeter')
            ->setHelp('This value is calculated from gpx files. Erase it to trigger recalculation on save.')
            ->hideOnIndex();

        yield IntegerField::new('elevationLoss')
            ->setLabel('Tiefenmeter')
            ->setHelp('This value is calculated from gpx files. Erase it to trigger recalculation on save.')
            ->hideOnIndex();

        yield IntegerField::new('durationInDays')
            ->setLabel('Dauer in Tagen')
            ->setFormTypeOption('disabled', 'disabled')
            ->hideOnIndex();
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return $this->get(EntityRepository::class)->createQueryBuilder(
            $searchDto,
            $entityDto,
            $fields,
            $filters
        );
    }


}
