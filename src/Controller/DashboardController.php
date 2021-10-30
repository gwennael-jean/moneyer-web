<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Provider\Bank\AccountProviderInterface;
use App\Service\RequestHandler;
use App\Service\Transfer\LivingWageComputer;
use App\Service\Transfer\TransferComputer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private AccountProviderInterface $accountProvider,
        private TransferComputer $transferComputer,
        private LivingWageComputer $livingWageComputer,
    )
    {
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function index(RequestHandler $requestHandler): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createNotFoundException("Only logged user can access to this page.");
        }

        $accounts = $this->accountProvider->getByUser($user);

        $transfers = $this->transferComputer->compute($accounts, $requestHandler->getDate(), $user);

        return $this->render('pages/dashboard/index.html.twig', [
            'date' => $requestHandler->getDate(),
            'accounts' => $accounts,
            'transfers' => $transfers,
            'livingWage' => $this->livingWageComputer->compute($transfers),
        ]);
    }
}
