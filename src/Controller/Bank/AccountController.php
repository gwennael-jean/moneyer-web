<?php

namespace App\Controller\Bank;

use App\Entity\Bank\Account;
use App\Entity\Bank\AccountShare;
use App\Form\Bank\AccountShareType;
use App\Form\Bank\AccountType;
use App\Security\Voter\Bank\AccountVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account/{account}/update', name: 'bank_account_update')]
    public function update(Request $request, Account $account): Response
    {
        $this->denyAccessUnlessGranted(AccountVoter::EDIT, $account);

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


    #[Route('/account/{account}/share', name: 'bank_account_share')]
    public function share(Request $request, Account $account): Response
    {
        $this->denyAccessUnlessGranted(AccountVoter::EDIT, $account);

        $accountShare = (new AccountShare())
            ->setAccount($account);

        $form = $this->createForm(AccountShareType::class, $accountShare);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($accountShare);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Account shared successfully.");
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/bank/account/share.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }
}
