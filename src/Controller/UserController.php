<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'liste_des_users', methods:['GET'])]
    public function getListeDesUsers(): JsonResponse
    {
        return new JsonResponse('coucou');
    }

    #[Route('/users', name: 'user_post', methods:['POST'])]
    public function createUser(): JsonResponse
    {
        return new JsonResponse('coucou');
    }

    #[Route('/user/{identifiant}', name: 'get_user_by_id', methods:['GET'])]
    public function getUserWithIdentifiant($identifiant): JsonResponse
    {
        return new JsonResponse('coucou '. $identifiant);
    }

    #[Route('/user/{identifiant}', name: 'udpate_user', methods:['PATCH'])]
    public function updateUser(): JsonResponse
    {
        return new JsonResponse('coucou');
    }

    #[Route('/user/{id}', name: 'delete_user_by_identifiant', methods:['DELETE'])]
    public function suprUser(): JsonResponse
    {
        return new JsonResponse('coucou');
    }
}
