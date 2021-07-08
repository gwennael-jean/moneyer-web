<?php

namespace App\Controller\Bank;

use App\Entity\Bank\Account;
use App\Form\Bank\AccountType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account/{account}/update', name: 'bank_account_update')]
    public function index(Request $request, Account $account): Response
    {
        $form = $this->createForm(AccountType::class, $account, [
            'form_type' => 'simple'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($account);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Account saved successfully.");
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/bank/account/update.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }
}
