<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Entity\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Psr\Container\ContainerInterface;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;


    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }

    public function register(string $username, string $plainPassword): User
    {
        $user = $this->userRepository->create($username, $plainPassword);
        $encodedPassword = $this->encoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);
        $user = $this->userRepository->save($user);
        return $user;
    }

    public function authenticate(string $username, string $password): ?User
    {
        $user = $this->findByUsername($username);
        return ($user && $this->encoder->isPasswordValid($user, $password) ? $user : null);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->userRepository->findOneByUsername($username);
    }
}
