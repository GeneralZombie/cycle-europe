<?php

namespace App\Controller\Admin;

use App\Admin\Field\ImageCloudField;
use App\Entity\RecordDistance;
use App\Entity\Tour;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class RecordDistanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RecordDistance::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort([
                'distance' => 'DESC',
                'elevationGain' => 'DESC',
            ])
            ->setPaginatorPageSize(50)
            ->setEntityLabelInSingular('Rekord Distanz')
            ->setEntityLabelInPlural('Rekorde Distanz');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title')
            ->setLabel('Titel');


        yield DateField::new('date')
            ->setLabel('Datum');

    
        yield IntegerField::new('distance')
            ->setLabel('Distanz')
            ->formatValue(function($value) {
                return intval(round($value / 1000)) . 'km';
            });

        yield IntegerField::new('elevationGain')
            ->setLabel('Höhenmeter');

        yield IntegerField::new('elevationLoss')
            ->setLabel('Tiefenmeter')
            ->hideOnIndex();

        yield AssociationField::new('tour')
            ->setLabel('Tour');
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
