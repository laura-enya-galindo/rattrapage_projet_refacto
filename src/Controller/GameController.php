<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/games', name: 'get_list_of_games', methods:['GET'])]
    public function getPartieList(): JsonResponse
    {
        return new JsonResponse('coucou');
    }

    #[Route('/games', name: 'create_game', methods:['POST'])]
    public function launchGame(): JsonResponse
    {
        return new JsonResponse('coucou');
    }

    #[Route('/game/{identifiant}', name: 'fetch_game', methods:['GET'])]
    public function getGameInfo($identifiant): JsonResponse
    {
        return new JsonResponse('coucou '. $identifiant);
    }

    #[Route('/game/{identifiant}', name: 'send_choice', methods:['PATCH'])]
    public function play(): JsonResponse
    {
        return new JsonResponse('coucou');
    }

    #[Route('/game/{id}', name: 'annuler_game', methods:['DELETE'])]
    public function deleteGame(): JsonResponse
    {
        return new JsonResponse('coucou');
    }
}
