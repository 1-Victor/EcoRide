<?php

namespace App\Controller\Admin;

use App\Entity\Reviews;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ReviewsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reviews::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            IntegerField::new('rating', 'Note'),
            TextareaField::new('comment', 'Commentaire'),
            BooleanField::new('is_validated', 'Validé ?')->renderAsSwitch(true),
            DateTimeField::new('created_at', 'Date de création'),
            AssociationField::new('author', 'Auteur'),
            AssociationField::new('targetUser', 'Chauffeur concerné'),
            AssociationField::new('carSharing', 'Trajet'),
        ];
    }
}
