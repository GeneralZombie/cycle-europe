<?php

namespace App\Controller\Admin;

use App\Entity\TourCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;

class TourCollectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TourCollection::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort([
                'title' => 'ASC',
            ])
            ->setPaginatorPageSize(50)
            ->setEntityLabelInSingular('Tourensammlung')
            ->setEntityLabelInPlural('Tourensammlungen');
    }

    public function configureFields(string $pageName): iterable
    {
        yield BooleanField::new('active')
            ->setLabel('Aktiv');
        yield TextField::new('title')
            ->setLabel('Titel');
        yield TextField::new('slug')
            ->setLabel('Slug');
        yield TextField::new('subtitle')
            ->setLabel('Untertitel')
            ->hideOnIndex();
        yield ArrayField::new('tours')
            ->setLabel('Touren')
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
