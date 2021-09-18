<?php

namespace App\Controller\Bank;

use App\Entity\Bank;
use App\Entity\User;
use App\Form\Bank\ChargeType;
use App\Service\Provider\Bank\ChargeProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChargeController extends AbstractController
{
    public function __construct(
        private ChargeProvider $chargeProvider
    )
    {
    }

    #[Route('/charges', name: 'bank_charge_list')]
    public function list(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createNotFoundException("Only logged user can access to this page.");
        }

        $charges = $this->chargeProvider->getByUser($user);

        return $this->render('pages/charge/list.html.twig', [
            'charges' => $charges,
        ]);
    }

    #[Route('/bank/account/{account}/charge/add', name: 'bank_charge_add')]
    #[Route('/bank/account/{account}/charge/{charge}/update', name: 'bank_charge_update')]
    #[ParamConverter('account', options: ['mapping' => ['account' => 'id']])]
    #[ParamConverter('charge', options: ['mapping' => ['charge' => 'id']])]
    public function add(Request $request, Bank\Account $account, ?Bank\Charge $charge = null): Response
    {
        $charge = $charge ?? (new Bank\Charge())
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

    #[Route('/bank/charge/{charge}/delete', name: 'bank_charge_delete')]
    public function delete(Request $request, Bank\Charge $charge)
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $this->getDoctrine()->getManager()->remove($charge);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Charge deleted successfully.");
            return $this->redirectToRoute('dashboard');
        }

        return $this->render("pages/bank/charge/delete.html.twig", [
            'charge' => $charge
        ]);
    }
}
