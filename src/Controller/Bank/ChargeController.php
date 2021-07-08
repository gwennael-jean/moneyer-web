<?php

namespace App\Controller\Bank;

use App\Entity\Bank\Account;
use App\Entity\Bank\Charge;
use App\Form\Bank\ChargeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChargeController extends AbstractController
{
    #[Route('/bank/account/{account}/charge/add', name: 'bank_charge_add')]
    public function add(Request $request, Account $account): Response
    {
        $charge = (new Charge())
            ->setAccount($account);

        $form = $this->createForm(ChargeType::class, $charge);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($charge);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Charge added successfully.");
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/bank/charge/index.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }
}
