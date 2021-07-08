<?php

namespace App\Controller\Bank;

use App\Entity\Bank\Account;
use App\Entity\User;
use App\Form\Bank\AccountType;
use App\Service\Provider\Bank\AccountProvider;
use App\Service\Transfer\TransferComputer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{

    #[Route('/account/{id}/update', name: 'account-update')]
    public function index(Request $request, Account $account): Response
    {
        $form = $this->createForm(AccountType::class, $account, [
            'form_type' => 'simple'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($account);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Account save successfully.");
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/bank/account/update.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }
}
