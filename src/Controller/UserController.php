<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Formatter\ApiResponseFormatter;
use App\Repository\UserRepository;
use App\Service\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository       $userRepository,
        private ApiResponseFormatter $apiResponseFormatter,
        private ValidatorInterface  $validator,
        private UserProvider         $userProvider,
    )
    {
    }

    #[Route('/users',
        name: 'app_user',
        methods: ['GET'])
    ]
    #[IsGranted('ROLE_ADMIN',
        message: 'You are not allowed to access to this function.')]
    public function index(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $transformedUser = [];

        foreach ($users as $user) {
            $transformedUser[] = $user->toArray();
        }

        return $this->apiResponseFormatter
            ->withData($transformedUser)
            ->response();
    }


    #[Route('/users/about',
        name: 'app_users_about',
        methods: ['GET'])
    ]
    #[IsGranted('ROLE_USER',
        message: 'You are not allowed to access the admin dashboard.')]
    public function showMe() : JsonResponse
    {
        $user = $this->getUser();

        return $this->apiResponseFormatter
            ->withData($user->toArray())
            ->response();
    }

    #[Route('/users/{id}',
        name: 'app_user_show',
        methods: ['GET'])
    ]

    #[IsGranted('ROLE_GET_USER_BY_ID')]
    public function show(int $id) : JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        if(!$user) {
            throw new UserNotFoundException();
        }

        return $this->apiResponseFormatter
            ->withData($user->toArray())
            ->response();
    }
    #[Route('/users',
        name: 'create_user',
        methods: ['POST'])
    ]
    #[IsGranted('ROLE_ADMIN',
        message: 'You are not allowed to access to this function.')]
    public function create(Request $request) : JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (empty($requestData)) {
            return $this->apiResponseFormatter
                ->withMessage('Invalid request')
                ->withStatus(Response::HTTP_BAD_REQUEST)
                ->response();
        }

        $user = $this->userProvider->createUser($requestData['email'],$requestData['password'] );

        return $this->apiResponseFormatter
            ->withData($user->toArray())
            ->withStatus(200)
            ->response();
    }

    #[Route('/users/{id}',
        name: 'update_user',
        methods: ['PATCH'])
    ]
    #[IsGranted('ROLE_ADMIN',
        message: 'You are not allowed to access to this function.')]
    public function update(Request $request, int $id, UserPasswordHasherInterface $passwordHasher) : JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if(!$user) {
            throw new UserNotFoundException();
        }

        $newUserData = json_decode($request->getContent(), true);

        (empty($newUserData['email'])) ?  : $user->setEmail($newUserData['email']);
        if(!empty($newUserData['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $newUserData['password']);
            $user->setPassword($hashedPassword);
        }
        $errors = $this->validator->validate($user);

        if(count($errors) > 0) {
            return $this->apiResponseFormatter
                ->withMessage('Invalid request')
                ->withStatus(Response::HTTP_BAD_REQUEST)
                ->withErrors([$errors->get(0)->getMessage()])
                ->response();
        }

        $this->userRepository->save($user);

        return $this->apiResponseFormatter
            ->withData($user->toArray())
            ->response();

    }

    #[IsGranted('ROLE_ADMIN',
        message: 'You are not allowed to access to this function.')]
    #[Route('/users/{id}',
        name: 'delete_user',
        methods: ['DELETE'])
    ]
    public function delete(int $id) : JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $this->userRepository->remove($user);

        return $this->apiResponseFormatter
            ->withMessage('User deleted successfully')
            ->withData($user->toArray())
            ->response();
    }

}
