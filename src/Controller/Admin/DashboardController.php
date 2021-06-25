<?php

namespace App\Controller\Admin;

use App\Entity\Bank\Account;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Moneyer')
            ->renderSidebarMinimized()
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::linkToCrud('User list', 'fa fa-users', User::class);

        yield MenuItem::section('Bank');
        yield MenuItem::linkToCrud('Account list', 'fa fa-university', Account::class);
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }
}
