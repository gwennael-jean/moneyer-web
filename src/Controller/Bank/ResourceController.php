<?php

namespace App\Controller\Bank;

use App\Entity\Bank\Account;
use App\Entity\Bank\Resource;
use App\Form\Bank\ResourceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResourceController extends AbstractController
{
    #[Route('/bank/account/{account}/resource/add', name: 'bank_resource_add')]
    #[Route('/bank/account/{account}/resource/{resource}/update', name: 'bank_resource_update')]
    #[ParamConverter('account', options: ['mapping' => ['account' => 'id']])]
    #[ParamConverter('resource', options: ['mapping' => ['resource' => 'id']])]
    public function add(Request $request, Account $account, ?Resource $resource = null): Response
    {
        $resource = $resource ?? (new Resource())
                ->setAccount($account);

        $form = $this->createForm(ResourceType::class, $resource);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', $resource->getId()
                ? "Resource modified successfully."
                : "Resource added successfully.");

            $this->getDoctrine()->getManager()->persist($resource);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dashboard');
        }

        $template = $resource->getId()
            ? "pages/bank/resource/update.html.twig"
            : "pages/bank/resource/add.html.twig";

        return $this->render($template, [
            'resource' => $resource,
            'form' => $form->createView(),
        ]);
    }
}
