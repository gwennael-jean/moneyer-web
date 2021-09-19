<?php

namespace App\Controller\Bank;

use App\Entity\Bank;
use App\Entity\User;
use App\Form\Bank\ResourceType;
use App\Repository\Bank\ResourceRepository;
use App\Service\Provider\Bank\ResourceProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResourceController extends AbstractController
{
    public function __construct(
        private ResourceRepository $resourceRepository
    )
    {
    }

    #[Route('/resources', name: 'bank_resource_list')]
    public function list(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createNotFoundException("Only logged user can access to this page.");
        }

        $resources = $this->resourceRepository->findByUser($user);

        return $this->render('pages/resource/list.html.twig', [
            'resources' => $resources,
        ]);
    }

    #[Route('/resource/add', name: 'bank_resource_add')]
    #[Route('/resource/{resource}/update', name: 'bank_resource_update')]
    #[ParamConverter('account', options: ['mapping' => ['account' => 'id']])]
    #[ParamConverter('resource', options: ['mapping' => ['resource' => 'id']])]
    public function add(Request $request, ?Bank\Resource $resource = null): Response
    {
        $resource = $resource ?? (new Bank\Resource());

        $form = $this->createForm(ResourceType::class, $resource);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', $resource->getId()
                ? "Resource modified successfully."
                : "Resource added successfully.");

            $this->getDoctrine()->getManager()->persist($resource);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bank_resource_list');
        }

        $template = $resource->getId()
            ? "pages/bank/resource/update.html.twig"
            : "pages/bank/resource/add.html.twig";

        return $this->render($template, [
            'resource' => $resource,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/resource/{resource}/delete', name: 'bank_resource_delete')]
    public function delete(Request $request, Bank\Resource $resource)
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $this->getDoctrine()->getManager()->remove($resource);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Resource deleted successfully.");
            return $this->redirectToRoute('bank_resource_list');
        }

        return $this->render("pages/bank/resource/delete.html.twig", [
            'resource' => $resource
        ]);
    }
}
