<?php

namespace App\Controller\Bank;

use App\Entity\Bank;
use App\Form\Bank\ChargeDistributionType;
use App\Service\Provider\Bank\AccountProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChargeDistributionController extends AbstractController
{
    public function __construct(
        private AccountProviderInterface $accountProvider
    )
    {
    }

    #[Route('/bank/charge/{charge}/distribution', name: 'bank_charge_distribution_index')]
    public function index(Request $request, Bank\Charge $charge): Response
    {
        $chargeDistribution = $charge->getChargeDistribution() ?? (new Bank\ChargeDistribution())
            ->setCharge($charge);

        $form = $this->createForm(ChargeDistributionType::class, $chargeDistribution, [
            'accounts' => $this->accountProvider->getByUser($this->getUser())
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', "Charge distribution modified successfully.");

            $this->getDoctrine()->getManager()->persist($chargeDistribution);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bank_charge_list');
        }

        return $this->render("pages/bank/charge-distribution/index.html.twig", [
            'charge' => $charge,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/bank/charge-distribution/{chargeDistribution}/delete', name: 'bank_charge_distribution_delete')]
    public function delete(Request $request, Bank\ChargeDistribution $chargeDistribution): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $this->getDoctrine()->getManager()->remove($chargeDistribution);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Resource deleted successfully.");
            return $this->redirectToRoute('dashboard');
        }

        return $this->render("pages/bank/charge-distribution/delete.html.twig", [
            'chargeDistribution' => $chargeDistribution
        ]);
    }
}
