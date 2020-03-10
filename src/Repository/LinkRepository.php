<?php

namespace App\Repository;

use App\Entity\Link;
use App\Entity\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    /**
     * @param string $original
     * @param string $pretty
     * @param User $user
     * @return Link
     */
    public function create(string $original, string $pretty, User $user): Link
    {
        $link = new Link();
        $link->setOriginal($original);
        $link->setPretty($pretty);
        $link->setUser($user);
        return $link;
    }

    public function findOneByIdAndUser(int $id, User $user): ?Link
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.id = :id')
            ->andWhere('l.user = :user')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByPrettyAndUser(string $pretty, User $user): ?Link
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.pretty = :pretty')
            ->andWhere('l.user = :user')
            ->setParameter('pretty', $pretty)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByOriginalAndUser(string $original, User $user): ?Link
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.original = :original')
            ->andWhere('l.user = :user')
            ->setParameter('original', $original)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Link $link
     * @return Link
     */
    public function save(Link $link): Link
    {
        $this->getEntityManager()->persist($link);
        $this->getEntityManager()->flush();
        return $link;
    }

    /**
     * @param Link $link
     */
    public function remove(Link $link)
    {
        $this->getEntityManager()->remove($link);
        $this->getEntityManager()->flush();
    }
}
