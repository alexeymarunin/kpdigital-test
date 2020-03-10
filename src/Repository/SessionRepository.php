<?php

namespace App\Repository;

use App\Entity\Session;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /**
     * @param string $cookieKey Cookie key value
     * @param string $username User name
     * @return Session
     */
    public function create(string $cookieKey, string $username): Session
    {
        $session = new Session();
        $session->setCookieKey($cookieKey);
        $session->setUsername($username);
        return $session;
    }

    /**
     * @param Session $session
     * @return Session
     */
    public function save(Session $session): Session
    {
        $this->getEntityManager()->persist($session);
        $this->getEntityManager()->flush();
        return $session;
    }

    /**
     * @param Session $session
     */
    public function remove($session)
    {
        $this->getEntityManager()->remove($session);
        $this->getEntityManager()->flush();
    }
}
