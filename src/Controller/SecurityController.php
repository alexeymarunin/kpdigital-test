<?php

namespace App\Controller;

use App\Service\UserService;
use App\Service\SessionService;
use App\Exception\InvalidArgumentException;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SecurityController extends AbstractController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var SessionService
     */
    private $sessionService;

    public function __construct(UserService $userService, SessionService $sessionService)
    {
        $this->userService = $userService;
        $this->sessionService = $sessionService;
    }

    /**
     * @Route("/login", name="login_user", methods={"POST"})
     */
    public function login(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $data = $this->getSerializer()->decode($request->getContent(), 'json');
        $username = $data['username'];
        $password = $data['password'];
        if (!$username) {
            throw new InvalidArgumentException('No username specified');
        }
        if (!$password) {
            throw new InvalidArgumentException('Empty password');
        }
        $user = $this->userService->authenticate($username, $password);
        if (!$user) {
            throw new BadCredentialsException();
        }
        $response = $this->json([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ]);
        $this->sessionService->startSession($user);
        $this->sessionService->setCredentials($response, $user);
        return $response;
    }

    /**
     * @Route("/register", name="register_user", methods={"POST"})
     */
    public function register(Request $request)
    {
        $data = $this->getSerializer()->decode($request->getContent(), 'json');
        $username = $data['username'];
        $password = $data['password'];
        if (!$username) {
            throw new InvalidArgumentException('Empty username');
        }
        if (!$password) {
            throw new InvalidArgumentException('Empty password');
        }
        $user = $this->userService->findByUsername($username);
        if ($user) {
            throw new InvalidArgumentException('User with such username has already registered');
        }
        $user = $this->userService->register($username, $password);
        return $this->forward(get_class($this) . '::login', $data);
    }

    /**
     * @Route("/logout", name="logout_user", methods={"GET","POST"})
     */
    public function logout()
    {
        $response = $this->json(null, 204);
        $user = $this->getUser();
        if ($user) {
            $this->sessionService->destroySession($user);
            $this->sessionService->setCredentials($response, null);
        }
        return $response;
    }

    private function getSerializer()
    {
        return $this->container->get('serializer');
    }
}
