<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserProvider
{
    public function __construct(
        private readonly UserRepository $userRepository)
    {
    }

    public function  createUser(string $email, string $password): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user);

        return $user;
    }

}
