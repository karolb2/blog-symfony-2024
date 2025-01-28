<?php

namespace App\Controller;

use App\Formatter\ApiResponseFormatter;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class OldUserController extends AbstractController
{
    public function __construct(
        private UserRepository $UserRepository,
        private ApiResponseFormatter $apiResponseFormatter
    )
    {
    }

    #[Route('/users2',
        name: 'app_user2',
        methods: ['GET'])
    ]
    public function index(): Response
    {
        $users = $this->UserRepository->findAll();

        $transformedUsers = [];
        foreach ($users as $user) {
            $transformedUsers[] = $user->toArray();
        }

        return $this->apiResponseFormatter
            ->withData($transformedUsers)
            ->response();
        }

    #[Route('/users2/{id}', name: 'app_user_show2', methods: ['GET'])]
    public function show(int $id){
        $user = $this->UserRepository->findOneBy(['id' => $id]);

        return $this->apiResponseFormatter
            ->withData($user->toArray())
            ->response();
    }

    #[Route('/users2', name: 'create_user2', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        dd($request->getContent());
       // return new JsonResponse();
    }

    #[Route('/users2', name: 'update_user2', methods: ['PATCH'])]
    public function update(int $id): JsonResponse
    {
        return new JsonResponse();
    }

    #[Route('/users2', name: 'delete_user2', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        return new JsonResponse();
    }
}
