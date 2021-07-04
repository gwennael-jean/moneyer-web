<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Provider\Bank\AccountProvider;
use App\Service\Transfer\TransferComputer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private AccountProvider $accountProvider,
        private TransferComputer $transferComputer,
    )
    {
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createNotFoundException("Only logged user can access to this page.");
        }

        $accounts = $this->accountProvider->getByUser($user);

        return $this->render('pages/dashboard/index.html.twig', [
            'accounts' => $accounts,
            'transfers' => $this->transferComputer->computeByUser($user, $accounts),
        ]);
    }
}
