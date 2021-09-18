<?php

namespace App\Controller\Bank;

use App\Entity\Bank\Account;
use App\Entity\Bank\AccountShare;
use App\Entity\User;
use App\Form\Bank\AccountShareType;
use App\Form\Bank\AccountType;
use App\Security\Voter\Bank\AccountVoter;
use App\Service\Provider\Bank\AccountProvider;
use App\Service\Transfer\LivingWageComputer;
use App\Service\Transfer\TransferComputer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    public function __construct(
        private AccountProvider $accountProvider,
    )
    {
    }

    #[Route('/accounts', name: 'bank_account_list')]
    public function list(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createNotFoundException("Only logged user can access to this page.");
        }

        $accounts = $this->accountProvider->getByUser($user);

        return $this->render('pages/account/list.html.twig', [
            'accounts' => $accounts,
        ]);
    }

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
        $this->denyAccessUnlessGranted(AccountVoter::SHARE, $account);

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

    #[Route('/account/{accountShare}', name: 'bank_account_unshare')]
    public function unshare(Request $request, AccountShare $accountShare): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {

            foreach ($accountShare->getAccount()->getCharges() as $charge) {
                if ($charge->hasChargeDistribution()) {
                    $charge->getChargeDistribution()->removeUser($accountShare->getUser());

                    $charge->getChargeDistribution()->getUsers()->count() > 1
                        ? $this->getDoctrine()->getManager()->persist($charge->getChargeDistribution())
                        : $this->getDoctrine()->getManager()->remove($charge->getChargeDistribution());

                    $this->getDoctrine()->getManager()->remove($accountShare);
                }
            }

            $this->getDoctrine()->getManager()->remove($accountShare);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Account share deleted successfully.");
            return $this->redirectToRoute('dashboard');
        }

        return $this->render("pages/bank/account/unshare.html.twig", [
            'accountShare' => $accountShare
        ]);
    }
}
