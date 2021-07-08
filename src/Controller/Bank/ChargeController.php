<?php

namespace App\Controller\Bank;

use App\Entity\Bank\Account;
use App\Entity\Bank\Charge;
use App\Form\Bank\ChargeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChargeController extends AbstractController
{
    #[Route('/bank/account/{account}/charge/add', name: 'bank_charge_add')]
    #[Route('/bank/account/{account}/charge/{charge}/update', name: 'bank_charge_update')]
    #[ParamConverter('account', options: ['mapping' => ['account' => 'id']])]
    #[ParamConverter('charge', options: ['mapping' => ['charge' => 'id']])]
    public function add(Request $request, Account $account, ?Charge $charge = null): Response
    {
        $charge = $charge ?? (new Charge())
            ->setAccount($account);

        $form = $this->createForm(ChargeType::class, $charge);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', $charge->getId()
                ? "Charge modified successfully."
                : "Charge added successfully.");

            $this->getDoctrine()->getManager()->persist($charge);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dashboard');
        }

        $template = $charge->getId()
            ? "pages/bank/charge/update.html.twig"
            : "pages/bank/charge/add.html.twig";

        return $this->render($template, [
            'charge' => $charge,
            'form' => $form->createView(),
        ]);
    }
}
