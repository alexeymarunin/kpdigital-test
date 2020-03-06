<?php

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Link      saveLink(string $original, $pretty)
 * @method void      removeLink(Link $link)
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    /**
     * @param string $original
     * @return Link
     */
    public function saveLink(string $original, string $pretty): Link
    {
        $link = new Link();
        $link->setOriginal($original);
        $link->setPretty($pretty);
        $this->getEntityManager()->persist($link);
        $this->getEntityManager()->flush();
        return $link;
    }

    /**
     * @param Link $link
     * @return Link
     */
    public function updateLink(Link $link): Link
    {
        $this->getEntityManager()->persist($link);
        $this->getEntityManager()->flush();
        return $link;
    }

    /**
     * @param Link $link
     */
    public function removeLink(Link $link)
    {
        $this->getEntityManager()->remove($link);
        $this->getEntityManager()->flush();
    }

    // /**
    //  * @return Link[] Returns an array of Link objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Link
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
