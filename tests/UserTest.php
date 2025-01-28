<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserProvider;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateUser()
    {
        $email = 'test@example.com';
        $password = 'password123';

        $userRepository = $this->createMock(UserRepository::class);

        $userRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $userService = new UserProvider($userRepository);
        $result = $userService->createUser($email, $password);

        //assertions
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($email, $result->getEmail());
        $this->assertEquals($password, $result->getPassword());

    }
}
