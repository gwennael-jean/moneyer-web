<?php

namespace App\Controller\Bank;

use App\Entity\Bank;
use App\Entity\User;
use App\Form\Bank\Resource\ResourceFilterType;
use App\Form\Bank\Resource\ResourceType;
use App\Repository\Bank\ResourceRepository;
use App\Service\RequestHandler;
use App\Util\Form\FormFilter;
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
    public function list(Request $request, RequestHandler $requestHandler): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createNotFoundException("Only logged user can access to this page.");
        }

        $form = $this->createForm(ResourceFilterType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        $resources = $this->resourceRepository->findByDateAndUser($requestHandler->getDate(), $user, new FormFilter($form));

        return $this->render('pages/bank/resource/list.html.twig', [
            'date' => $requestHandler->getDate(),
            'resources' => $resources,
            'formFilter' => $form->createView(),
        ]);
    }

    #[Route('/resource/add', name: 'bank_resource_add')]
    #[Route('/resource/{resource}/update', name: 'bank_resource_update')]
    #[ParamConverter('account', options: ['mapping' => ['account' => 'id']])]
    #[ParamConverter('resource', options: ['mapping' => ['resource' => 'id']])]
    public function add(Request $request, ?Bank\Resource $resource = null): Response
    {
        $resource = $resource ?? (new Bank\Resource());

        $form = $this->createForm(ResourceType::class, $resource, [
            'user' => $this->getUser()
        ]);

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
