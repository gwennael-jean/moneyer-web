<?php

namespace App\Controller\Bank;

use App\Entity\Bank\Account;
use App\Entity\Bank\Resource;
use App\Form\Bank\ResourceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResourceController extends AbstractController
{
    #[Route('/bank/account/{account}/resource/add', name: 'bank_resource_add')]
    public function add(Request $request, Account $account): Response
    {
        $resource = (new Resource())
            ->setAccount($account);

        $form = $this->createForm(ResourceType::class, $resource);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($resource);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Resource added successfully.");
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/bank/resource/index.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }
}
