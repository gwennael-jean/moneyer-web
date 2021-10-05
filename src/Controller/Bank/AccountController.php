<?php

namespace App\Controller\Bank;

use App\Entity\Bank\Account;
use App\Entity\Bank\AccountShare;
use App\Entity\User;
use App\Form\Bank\Account\AccountFilterType;
use App\Form\Bank\Account\AccountType;
use App\Form\Bank\AccountShareType;
use App\Notification\Bank\AccountShareNotification;
use App\Notification\Bank\AccountUnshareNotification;
use App\Notification\UserRecipient;
use App\Repository\Bank\AccountRepository;
use App\Security\Voter\Bank\AccountVoter;
use App\Service\RequestHandler;
use App\Util\Form\FormFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    public function __construct(
        private AccountRepository $accountRepository,
    )
    {
    }

    #[Route('/accounts', name: 'bank_account_list')]
    public function list(Request $request, RequestHandler $requestHandler): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException("Only logged user can access to this page.");
        }

        $form = $this->createForm(AccountFilterType::class);

        $form->handleRequest($request);

        $accounts = $this->accountRepository->findByUser($user, new FormFilter($form));

        return $this->render('pages/bank/account/list.html.twig', [
            'date' => $requestHandler->getDate(),
            'accounts' => $accounts,
            'formFilter' => $form->createView()
        ]);
    }

    #[Route('/account/add', name: 'bank_account_add')]
    #[Route('/account/{account}/update', name: 'bank_account_update')]
    public function update(Request $request, ?Account $account): Response
    {
        $account = $account ?? (new Account());

        $this->denyAccessUnlessGranted(AccountVoter::EDIT, $account);

        $form = $this->createForm(AccountType::class, $account);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($account);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Account saved successfully.");
            return $this->redirectToRoute('bank_account_list');
        }

        return $this->render('pages/bank/account/update.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/{account}/share', name: 'bank_account_share')]
    public function share(Request $request, Account $account, NotifierInterface $notifier): Response
    {
        $this->denyAccessUnlessGranted(AccountVoter::SHARE, $account);

        $accountShare = (new AccountShare())
            ->setAccount($account);

        $form = $this->createForm(AccountShareType::class, $accountShare);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($accountShare);
            $this->getDoctrine()->getManager()->flush();

            $notifier->send(new AccountShareNotification($accountShare, $this->getUser()), new UserRecipient($accountShare->getUser()));

            $this->addFlash('success', "Account shared successfully.");
            return $this->redirectToRoute('bank_account_list');
        }

        return $this->render('pages/bank/account/share.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/{accountShare}', name: 'bank_account_unshare')]
    public function unshare(Request $request, AccountShare $accountShare, NotifierInterface $notifier): Response
    {
        $this->denyAccessUnlessGranted(AccountVoter::SHARE, $accountShare->getAccount());

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

            $notifier->send(new AccountUnshareNotification($accountShare, $this->getUser()), new UserRecipient($accountShare->getUser()));

            $this->addFlash('success', "Account share deleted successfully.");
            return $this->redirectToRoute('bank_account_list');
        }

        return $this->render("pages/bank/account/unshare.html.twig", [
            'accountShare' => $accountShare
        ]);
    }

    #[Route('/account/{account}/delete', name: 'bank_account_delete')]
    public function delete(Request $request, Account $account)
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $this->getDoctrine()->getManager()->remove($account);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Account deleted successfully.");
            return $this->redirectToRoute('bank_account_list');
        }

        return $this->render("pages/bank/account/delete.html.twig", [
            'account' => $account
        ]);
    }
}
