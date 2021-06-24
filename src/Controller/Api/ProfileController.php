<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/profile', name: 'api_profile', methods: 'GET')]
    public function index(Request $request)
    {
        return $this->json($this->getUser(), Response::HTTP_OK, [], [
            'groups' => 'user:read'
        ]);
    }
}
