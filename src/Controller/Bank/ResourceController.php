<?php

namespace App\Controller\Bank;

use App\Entity\Bank;
use App\Form\Bank\ResourceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResourceController extends AbstractController
{
    #[Route('/bank/account/{account}/resource/add', name: 'bank_resource_add')]
    #[Route('/bank/account/{account}/resource/{resource}/update', name: 'bank_resource_update')]
    #[ParamConverter('account', options: ['mapping' => ['account' => 'id']])]
    #[ParamConverter('resource', options: ['mapping' => ['resource' => 'id']])]
    public function add(Request $request, Bank\Account $account, ?Bank\Resource $resource = null): Response
    {
        $resource = $resource ?? (new Bank\Resource())
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

    #[Route('/bank/resource/{resource}/delete', name: 'bank_resource_delete')]
    public function delete(Request $request, Bank\Resource $resource)
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $this->getDoctrine()->getManager()->remove($resource);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Resource deleted successfully.");
            return $this->redirectToRoute('dashboard');
        }

        return $this->render("pages/bank/resource/delete.html.twig", [
            'resource' => $resource
        ]);
    }
}
