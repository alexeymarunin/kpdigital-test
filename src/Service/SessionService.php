<?php

namespace App\Service;

use App\Repository\SessionRepository;
use App\Entity\Session;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\User\UserInterface;

class SessionService
{
    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function findUsername(string $cookieKey): ?string
    {
        $session = $this->sessionRepository->findOneByCookieKey($cookieKey);
        return $session ? $session->getUsername() : null;
    }

    public function startSession(UserInterface $user): Session
    {
        $username = $user->getUsername();
        $cookieKey = $this->generateSessionKey($user);
        $session = $this->sessionRepository->create($cookieKey, $username);
        return $this->sessionRepository->save($session);
        return $session;
    }

    public function checkCredentials(string $credentials, UserInterface $user): bool
    {
        $cookieKey = $this->generateSessionKey($user);
        return $credentials === $cookieKey;
    }

    public function destroySession(UserInterface $user)
    {
        $session = $this->sessionRepository->findOneByUsername($user->getUsername());
        $this->sessionRepository->remove($session);
    }

    public function getCredentials(Request $request): ?string
    {
        return $request->cookies->get('_session', '');
    }

    public function setCredentials(Response $response, ?UserInterface $user)
    {
        if ($user) {
            $cookieKey = $this->generateSessionKey($user);
            $response->headers->setCookie(Cookie::create('_session', $cookieKey));
        } else {
            $response->headers->removeCookie('_session');
        }
    }

    private function generateSessionKey(UserInterface $user): string
    {
        return hash('sha1', $user->getUsername() . $user->getPassword());
    }
}
