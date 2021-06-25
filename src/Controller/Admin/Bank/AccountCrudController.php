<?php

namespace App\Controller\Admin\Bank;

use App\Entity\Bank\Account;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_INDEX) {
            yield IdField::new('id');
        }

        yield TextField::new('name');
        yield AssociationField::new('owner');
        yield AssociationField::new('resources');
    }
}
